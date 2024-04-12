# Application
* Application Start Arguments Collection And Convert
* ApplicationContext
* Class Regist Center And AutoLoad
* Application Start Model
# config
* package.json
* example for modules
  * **ApplicationStart Config** (application.json.example)
  * **DB Config** (dbconfig.json.example system.json.example)
  * **Redis Cache Server Config** (redis.json.example)
  * **Wechat Robot Config** (wechat.json.example)
  * **Email Config** (mail.json.example)
# core
## annoation
* Annoation Collection Util (AnnoationRule)
  * Lite example @Anno
  * Normal example @Anno("value")
  * Relation example @Anno(value = "value", relation = AnnoRelation.AND)
  * List example @AnnoList value1 value2
* Annotation Enum Definition
  * Element Type (Class, Method, Field, PARAMETER, ANYWHERE)
  * Policy Enum (Runtime, Build, Active)
  * Value Type Enum (Lite, Normal, Relation, List)
* Annotation Abstract Class
* Aspect Abstract Interface (two model for aspect)
  * Build Time Aspect
  * Run Time Aspect
* The Object Of Process Pointcut (RunTimeProcessPoint)
* The Object Of Function For Aspect (RunTimeFunction)
## dependency collect and check
## dependency injection
* Bean Definition
* Collection framework
* Exception Definition
* Rich Reflection API
* Basic Utils
* Extra API
  * Configuration Factory
  * Environment
  * Gear Startter
  * System Utils
  * Trace
  * Logger Collect
# modules
* dependency collect and check (package.info)
* config collect and check