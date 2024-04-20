<?php
class AnnoationStarter implements EzStarter
{

    /**
     * @var array<Aspect>
     */
    private $aspectList = [];

    /**
     * @throws ReflectionException
     */
    public function init()
    {
        $annoList = $this->fetchComponentAspect();
        $annoList2 = $this->fetchBeanAspect();
        $this->aspectList = array_merge($annoList, $annoList2);
    }

    /**
     * @param EzReflectionClass $reflection
     * @param array<Aspect> $aspectList
     * @return void
     */
    private function fetchAnnoFromClass($reflection, array &$aspectList, array &$twichClassAnno) {
        $classAnnoList = $reflection->getAnnoationList();
        foreach($classAnnoList as $classAnno){
            $aspectClass = $this->buildPoorAspect($classAnno);
            if (is_null($aspectClass)) {
                continue;
            }
            $aspectClass->setAtClass($reflection);
            if(!is_null($aspectClass->getDependConf())){
                $twichClassAnno[] = $aspectClass;
            }else{
                $aspectList[] = $aspectClass;
            }
        }
    }

    /**
     * @param EzReflectionClass $reflection
     * @param array<Aspect> $aspectList
     * @return void
     */
    private function fetchAnnoFromMethod($reflection, array &$aspectList, array &$aspectMethodList) {
        $reflectionMethods = $reflection->getMethods();
        foreach($reflectionMethods as $reflectionMethod){
            $methodAnnoList = $reflectionMethod->getAnnoationList();
            foreach($methodAnnoList as $methodAnno){
                $aspectMethod = $this->buildPoorAspect($methodAnno);
                if (is_null($aspectMethod)) {
                    continue;
                }
                $aspectMethod->setAtClass($reflection);
                $aspectMethod->setAtMethod($reflectionMethod);
                if($aspectMethod->isCombination()){
                    $aspectMethodList[$aspectMethod->getAnnoName()][] = $aspectMethod;
                }else{
                    $aspectList[] = $aspectMethod;
                }
            }
        }
    }

    /**
     * @param EzReflectionClass $reflection
     * @param array<Aspect> $aspectList
     * @param array<string, array<EzReflectionProperty>> $aspectPropertyList
     * @return void
     */
    private function fetchAnnoFromProperty($reflection, array &$aspectList, array &$aspectPropertyList) {
        $reflectionProperties = $reflection->getProperties();
        foreach($reflectionProperties as $reflectionProperty){
            $propertyAnnoList = $reflectionProperty->getAnnoationList();
            foreach($propertyAnnoList as $propertyAnno){
                $aspectProperty = $this->buildPoorAspect($propertyAnno);
                if (is_null($aspectProperty)) {
                    continue;
                }
                $aspectProperty->setAtClass($reflection);
                $aspectProperty->setAtProperty($reflectionProperty);
                if($aspectProperty->isCombination()){
                    $aspectPropertyList[$aspectProperty->getAnnoName()][] = $aspectProperty;
                }else{
                    $aspectList[] = $aspectProperty;
                }
            }
        }
    }

    private function fetchAnnoForDepend($twichClassAspect, $aspectMethodList, $aspectPropertyList, array &$aspectList) {
        foreach($twichClassAspect as $classAspect){
            /**
             * @var Aspect $classAspect
             */
            $dependClassList = $classAspect->getDependConf();
            foreach($dependClassList as $dependClass){
                $classAspect->addDepend($aspectMethodList[$dependClass]??[]);
                $classAspect->addDepend($aspectPropertyList[$dependClass]??[]);
            }
            $aspectList[] = $classAspect;
        }
    }

    private function fetchBeanAspect(){
        $aspectList = [];
        foreach(BeanFinder::get()->getAll(DynamicProxy::class) as $obj) {
            /**
             * @var DynamicProxy $obj
             */
            $reflection = $obj->__CALL__getReflectionClass();
            /**
             * class 和 method或property 有关联的情况，将classAspect收集起来
             * 调用fetchAnnoForDepend再次处理
             */
            $twichClassAnno = [];
            $aspectMethodList = [];
            $aspectPropertyList = [];
            $this->fetchAnnoFromClass($reflection, $aspectList, $twichClassAnno);
            $this->fetchAnnoFromMethod($reflection, $aspectList, $aspectMethodList);
            $this->fetchAnnoFromProperty($reflection, $aspectList, $aspectPropertyList);
            $this->fetchAnnoForDepend($twichClassAnno, $aspectMethodList, $aspectPropertyList, $aspectList);
        }
        return $aspectList;
    }

    private function fetchComponentAspect() {
        $aspectList = [];
        if (empty(Application::getContext()->getGlobalComponentClass())) {
            return $aspectList;
        }
        foreach (Application::getContext()->getGlobalComponentClass() as $componentClassName) {
            $reflection = new EzReflectionClass($componentClassName);
            $twichClassAspect = [];
            $aspectMethodList = [];
            $aspectPropertyList = [];
            $this->fetchAnnoFromClass($reflection, $aspectList, $twichClassAnno);
            $this->fetchAnnoFromMethod($reflection, $aspectList, $aspectMethodList);
            $this->fetchAnnoFromProperty($reflection, $aspectList, $aspectPropertyList);
            $this->fetchAnnoForDepend($twichClassAspect, $aspectMethodList, $aspectPropertyList, $aspectList);
        }
        return $aspectList;
    }

    public function start(){
        foreach($this->aspectList as $aspect){
            if (!$aspect->check()) {
                continue;
            }
            var_dump($aspect, get_parent_class($aspect));
            if ($aspect instanceof BuildAspect) {
                $aspect->adhere();
            }
            if ($aspect instanceof RunTimeAspect) {
                $aspect->around();
            }
        }
    }



    /**
     * @param AnnoationElement $annoItem
     * @return Aspect
     * @throws Exception
     */
    private function buildPoorAspect(AnnoationElement $annoItem){
        $k = $annoItem->annoName;
        $v = $annoItem->getValue();
        DBC::assertNotEmpty($v->constStruct(), "[Gear] Anno $k Must Defined Const STRUCT!");
        $target = $v->constTarget();
        if (EzDataUtils::isList($target)) {
            DBC::assertTrue(in_array($annoItem->at, $target),
                "[Gear] Anno $k Must Used At ".AnnoElementType::getDesc($target)."!");
        } else {
            DBC::assertEquals($target, $annoItem->at, "[Gear] Anno $k Must Used At ".AnnoElementType::getDesc($target)."!");
        }
        $dependConf = $v instanceof AnnoationCombination ? $v->constDepend() : [];
        $policy = $v->constPolicy();
        DBC::assertNotEmpty($policy, "[Gear] Anno $k Must Defined Const POLICY!");
        $aspectClass = $v->constAspect();
        //Runtime为必填 才需要检查
        if (AnnoPolicyEnum::POLICY_RUNTIME == $policy) {
            DBC::assertTrue($aspectClass, "[Gear] Anno $k Must Defined Const ASPECT!");
        } else if (!$aspectClass) {
            //如果是BuildPolicy，又没定义过Aspect类走空逻辑
            return null;
        }
        /**
         * @var $aspect Aspect
         */
        $aspect = new $aspectClass;
        $aspect->setAnnoName($k);
        $aspect->setValue($v);
        $aspect->setIsCombination(is_subclass_of($v, AnnoationCombination::class));
        $aspect->setTarget($target);
        $aspect->setDependConf($dependConf);
        return $aspect;
    }

}