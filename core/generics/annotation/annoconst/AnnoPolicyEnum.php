<?php

class AnnoPolicyEnum
{
    /**
     * 在运行的逻辑过程中生效，用来获取关联信息
     * 以便在逻辑处理中使用
     */
    public const POLICY_RUNTIME = "RUNTIME";

    /**
     * 在构建初始化时生效，用来控制target运行时的行为
     * 此类型的注解应被标注在Bean上面，并且要实现对应的Aspect类
     * 当进入到被标注的Bean的代码逻辑之后，可以实现对代码逻辑的增强
     */
    public const POLICY_BUILD = "BUILD";

}
