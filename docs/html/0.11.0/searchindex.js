Search.setIndex({envversion:46,filenames:["api/AbstractData","api/ControllerProvider","api/DataFactoryInterface","api/Entity","api/EntityDefinition","api/EntityDefinitionFactory","api/EntityDefinitionFactoryInterface","api/EntityDefinitionValidator","api/EntityDefinitionValidatorInterface","api/EntityValidator","api/FileProcessorInterface","api/ManyValidator","api/MimeTypes","api/MySQLData","api/MySQLDataFactory","api/ReferenceValidator","api/ServiceProvider","api/SimpleFilesystemFileProcessor","api/StreamedFileResponse","api/TwigExtensions","api/UniqueValidator","index","manual/addons","manual/constraints","manual/datastructures","manual/datatypes","manual/definitionvalidation","manual/events","manual/extendedfeatures","manual/introduction","manual/layouts","manual/setup","manual/templates"],objects:{"":{"AbstractData::$definition":[0,1,1,""],"AbstractData::$events":[0,1,1,""],"AbstractData::$fileProcessor":[0,1,1,""],"AbstractData::DELETION_FAILED_EVENT":[0,2,1,""],"AbstractData::DELETION_FAILED_STILL_REFERENCED":[0,2,1,""],"AbstractData::DELETION_SUCCESS":[0,2,1,""],"AbstractData::countBy":[0,0,1,""],"AbstractData::create":[0,0,1,""],"AbstractData::createEmpty":[0,0,1,""],"AbstractData::createFiles":[0,0,1,""],"AbstractData::delete":[0,0,1,""],"AbstractData::deleteChildren":[0,0,1,""],"AbstractData::deleteFile":[0,0,1,""],"AbstractData::deleteFiles":[0,0,1,""],"AbstractData::doDelete":[0,0,1,""],"AbstractData::enrichEntityWithMetaData":[0,0,1,""],"AbstractData::get":[0,0,1,""],"AbstractData::getDefinition":[0,0,1,""],"AbstractData::getFormFields":[0,0,1,""],"AbstractData::getIdToNameMap":[0,0,1,""],"AbstractData::getManyFields":[0,0,1,""],"AbstractData::getReferenceIds":[0,0,1,""],"AbstractData::hasManySet":[0,0,1,""],"AbstractData::hydrate":[0,0,1,""],"AbstractData::listEntries":[0,0,1,""],"AbstractData::performOnFiles":[0,0,1,""],"AbstractData::popEvent":[0,0,1,""],"AbstractData::pushEvent":[0,0,1,""],"AbstractData::renderFile":[0,0,1,""],"AbstractData::shouldExecuteEvents":[0,0,1,""],"AbstractData::update":[0,0,1,""],"AbstractData::updateFiles":[0,0,1,""],"ControllerProvider::buildUpListFilter":[1,0,1,""],"ControllerProvider::connect":[1,0,1,""],"ControllerProvider::create":[1,0,1,""],"ControllerProvider::delete":[1,0,1,""],"ControllerProvider::deleteFile":[1,0,1,""],"ControllerProvider::edit":[1,0,1,""],"ControllerProvider::getAfterDeleteRedirectParameters":[1,0,1,""],"ControllerProvider::getNotFoundPage":[1,0,1,""],"ControllerProvider::modifyEntity":[1,0,1,""],"ControllerProvider::modifyFilesAndSetFlashBag":[1,0,1,""],"ControllerProvider::renderFile":[1,0,1,""],"ControllerProvider::setLocale":[1,0,1,""],"ControllerProvider::setValidationFailedFlashes":[1,0,1,""],"ControllerProvider::setupI18n":[1,0,1,""],"ControllerProvider::setupRoutes":[1,0,1,""],"ControllerProvider::setupTemplates":[1,0,1,""],"ControllerProvider::show":[1,0,1,""],"ControllerProvider::showList":[1,0,1,""],"ControllerProvider::staticFile":[1,0,1,""],"DataFactoryInterface::createData":[2,0,1,""],"Entity::$definition":[3,1,1,""],"Entity::$entity":[3,1,1,""],"Entity::__construct":[3,0,1,""],"Entity::get":[3,0,1,""],"Entity::getDefinition":[3,0,1,""],"Entity::getRaw":[3,0,1,""],"Entity::populateViaRequest":[3,0,1,""],"Entity::set":[3,0,1,""],"Entity::toType":[3,0,1,""],"EntityDefinition::$children":[4,1,1,""],"EntityDefinition::$childrenLabelFields":[4,1,1,""],"EntityDefinition::$deleteCascade":[4,1,1,""],"EntityDefinition::$fields":[4,1,1,""],"EntityDefinition::$filter":[4,1,1,""],"EntityDefinition::$initialSortAscending":[4,1,1,""],"EntityDefinition::$initialSortField":[4,1,1,""],"EntityDefinition::$label":[4,1,1,""],"EntityDefinition::$listFields":[4,1,1,""],"EntityDefinition::$locale":[4,1,1,""],"EntityDefinition::$localeLabels":[4,1,1,""],"EntityDefinition::$pageSize":[4,1,1,""],"EntityDefinition::$serviceProvider":[4,1,1,""],"EntityDefinition::$standardFieldLabels":[4,1,1,""],"EntityDefinition::$table":[4,1,1,""],"EntityDefinition::__construct":[4,0,1,""],"EntityDefinition::addChild":[4,0,1,""],"EntityDefinition::checkFieldNames":[4,0,1,""],"EntityDefinition::getChildren":[4,0,1,""],"EntityDefinition::getChildrenLabelFields":[4,0,1,""],"EntityDefinition::getEditableFieldNames":[4,0,1,""],"EntityDefinition::getField":[4,0,1,""],"EntityDefinition::getFieldLabel":[4,0,1,""],"EntityDefinition::getFieldNames":[4,0,1,""],"EntityDefinition::getFilter":[4,0,1,""],"EntityDefinition::getFilteredFieldNames":[4,0,1,""],"EntityDefinition::getInitialSortField":[4,0,1,""],"EntityDefinition::getLabel":[4,0,1,""],"EntityDefinition::getListFields":[4,0,1,""],"EntityDefinition::getLocale":[4,0,1,""],"EntityDefinition::getPageSize":[4,0,1,""],"EntityDefinition::getPublicFieldNames":[4,0,1,""],"EntityDefinition::getReadOnlyFields":[4,0,1,""],"EntityDefinition::getServiceProvider":[4,0,1,""],"EntityDefinition::getSubTypeField":[4,0,1,""],"EntityDefinition::getTable":[4,0,1,""],"EntityDefinition::getType":[4,0,1,""],"EntityDefinition::isDeleteCascade":[4,0,1,""],"EntityDefinition::isInitialSortAscending":[4,0,1,""],"EntityDefinition::setChildrenLabelFields":[4,0,1,""],"EntityDefinition::setDeleteCascade":[4,0,1,""],"EntityDefinition::setField":[4,0,1,""],"EntityDefinition::setFieldLabel":[4,0,1,""],"EntityDefinition::setFilter":[4,0,1,""],"EntityDefinition::setInitialSortAscending":[4,0,1,""],"EntityDefinition::setInitialSortField":[4,0,1,""],"EntityDefinition::setLabel":[4,0,1,""],"EntityDefinition::setListFields":[4,0,1,""],"EntityDefinition::setLocale":[4,0,1,""],"EntityDefinition::setPageSize":[4,0,1,""],"EntityDefinition::setServiceProvider":[4,0,1,""],"EntityDefinition::setTable":[4,0,1,""],"EntityDefinition::setType":[4,0,1,""],"EntityDefinitionFactory::createEntityDefinition":[5,0,1,""],"EntityDefinitionFactoryInterface::createEntityDefinition":[6,0,1,""],"EntityDefinitionValidator::validate":[7,0,1,""],"EntityDefinitionValidatorInterface::validate":[8,0,1,""],"EntityValidator::$definition":[9,1,1,""],"EntityValidator::$entity":[9,1,1,""],"EntityValidator::__construct":[9,0,1,""],"EntityValidator::buildUpData":[9,0,1,""],"EntityValidator::buildUpRules":[9,0,1,""],"EntityValidator::fieldConstraintsToRules":[9,0,1,""],"EntityValidator::fieldTypeToRules":[9,0,1,""],"EntityValidator::validate":[9,0,1,""],"FileProcessorInterface::createFile":[10,0,1,""],"FileProcessorInterface::deleteFile":[10,0,1,""],"FileProcessorInterface::renderFile":[10,0,1,""],"FileProcessorInterface::updateFile":[10,0,1,""],"ManyValidator::getInvalidDetails":[11,0,1,""],"ManyValidator::isValid":[11,0,1,""],"MimeTypes::$mimeTypes":[12,1,1,""],"MimeTypes::getMimeType":[12,0,1,""],"MimeTypes::getMimeTypeByExtension":[12,0,1,""],"MimeTypes::getMimeTypeByFileInfo":[12,0,1,""],"MySQLData::$database":[13,1,1,""],"MySQLData::$definition":[13,1,1,""],"MySQLData::$events":[13,1,1,""],"MySQLData::$fileProcessor":[13,1,1,""],"MySQLData::$useUUIDs":[13,1,1,""],"MySQLData::DELETION_FAILED_EVENT":[13,2,1,""],"MySQLData::DELETION_FAILED_STILL_REFERENCED":[13,2,1,""],"MySQLData::DELETION_SUCCESS":[13,2,1,""],"MySQLData::__construct":[13,0,1,""],"MySQLData::addFilter":[13,0,1,""],"MySQLData::addPagination":[13,0,1,""],"MySQLData::addSort":[13,0,1,""],"MySQLData::countBy":[13,0,1,""],"MySQLData::create":[13,0,1,""],"MySQLData::createEmpty":[13,0,1,""],"MySQLData::createFiles":[13,0,1,""],"MySQLData::delete":[13,0,1,""],"MySQLData::deleteChildren":[13,0,1,""],"MySQLData::deleteFile":[13,0,1,""],"MySQLData::deleteFiles":[13,0,1,""],"MySQLData::doDelete":[13,0,1,""],"MySQLData::enrichEntityWithMetaData":[13,0,1,""],"MySQLData::enrichWithMany":[13,0,1,""],"MySQLData::enrichWithManyField":[13,0,1,""],"MySQLData::enrichWithReference":[13,0,1,""],"MySQLData::fetchReferencesForField":[13,0,1,""],"MySQLData::generateUUID":[13,0,1,""],"MySQLData::get":[13,0,1,""],"MySQLData::getDefinition":[13,0,1,""],"MySQLData::getFormFields":[13,0,1,""],"MySQLData::getIdToNameMap":[13,0,1,""],"MySQLData::getManyFields":[13,0,1,""],"MySQLData::getManyIds":[13,0,1,""],"MySQLData::getReferenceIds":[13,0,1,""],"MySQLData::hasChildren":[13,0,1,""],"MySQLData::hasManySet":[13,0,1,""],"MySQLData::hydrate":[13,0,1,""],"MySQLData::listEntries":[13,0,1,""],"MySQLData::performOnFiles":[13,0,1,""],"MySQLData::popEvent":[13,0,1,""],"MySQLData::pushEvent":[13,0,1,""],"MySQLData::renderFile":[13,0,1,""],"MySQLData::saveMany":[13,0,1,""],"MySQLData::setValuesAndParameters":[13,0,1,""],"MySQLData::shouldExecuteEvents":[13,0,1,""],"MySQLData::update":[13,0,1,""],"MySQLData::updateFiles":[13,0,1,""],"MySQLDataFactory::$database":[14,1,1,""],"MySQLDataFactory::$useUUIDs":[14,1,1,""],"MySQLDataFactory::__construct":[14,0,1,""],"MySQLDataFactory::createData":[14,0,1,""],"ReferenceValidator::getInvalidDetails":[15,0,1,""],"ReferenceValidator::isValid":[15,0,1,""],"ServiceProvider::$datas":[16,1,1,""],"ServiceProvider::$manageI18n":[16,1,1,""],"ServiceProvider::configureDefinition":[16,0,1,""],"ServiceProvider::createDefinition":[16,0,1,""],"ServiceProvider::getData":[16,0,1,""],"ServiceProvider::getEntities":[16,0,1,""],"ServiceProvider::getLocaleLabels":[16,0,1,""],"ServiceProvider::getLocales":[16,0,1,""],"ServiceProvider::getTemplate":[16,0,1,""],"ServiceProvider::init":[16,0,1,""],"ServiceProvider::initChildren":[16,0,1,""],"ServiceProvider::initLocales":[16,0,1,""],"ServiceProvider::initMissingServiceProviders":[16,0,1,""],"ServiceProvider::isManagingI18n":[16,0,1,""],"ServiceProvider::readYaml":[16,0,1,""],"ServiceProvider::register":[16,0,1,""],"ServiceProvider::setLocale":[16,0,1,""],"ServiceProvider::validateEntityDefinition":[16,0,1,""],"SimpleFilesystemFileProcessor::$basePath":[17,1,1,""],"SimpleFilesystemFileProcessor::__construct":[17,0,1,""],"SimpleFilesystemFileProcessor::createFile":[17,0,1,""],"SimpleFilesystemFileProcessor::deleteFile":[17,0,1,""],"SimpleFilesystemFileProcessor::getPath":[17,0,1,""],"SimpleFilesystemFileProcessor::renderFile":[17,0,1,""],"SimpleFilesystemFileProcessor::updateFile":[17,0,1,""],"StreamedFileResponse::getStreamedFileFunction":[18,0,1,""],"TwigExtensions::formatDate":[19,0,1,""],"TwigExtensions::formatDateTime":[19,0,1,""],"TwigExtensions::formatFloat":[19,0,1,""],"TwigExtensions::formatTime":[19,0,1,""],"TwigExtensions::getLanguageName":[19,0,1,""],"TwigExtensions::registerTwigExtensions":[19,0,1,""],"UniqueValidator::getInvalidDetails":[20,0,1,""],"UniqueValidator::isValid":[20,0,1,""],"UniqueValidator::isValidUnique":[20,0,1,""],"UniqueValidator::isValidUniqueMany":[20,0,1,""],AbstractData:[0,3,1,""],ControllerProvider:[1,3,1,""],DataFactoryInterface:[2,4,1,""],Entity:[3,3,1,""],EntityDefinition:[4,3,1,""],EntityDefinitionFactory:[5,3,1,""],EntityDefinitionFactoryInterface:[6,4,1,""],EntityDefinitionValidator:[7,3,1,""],EntityDefinitionValidatorInterface:[8,4,1,""],EntityValidator:[9,3,1,""],FileProcessorInterface:[10,4,1,""],ManyValidator:[11,3,1,""],MimeTypes:[12,3,1,""],MySQLData:[13,3,1,""],MySQLDataFactory:[14,3,1,""],ReferenceValidator:[15,3,1,""],ServiceProvider:[16,3,1,""],SimpleFilesystemFileProcessor:[17,3,1,""],StreamedFileResponse:[18,3,1,""],TwigExtensions:[19,3,1,""],UniqueValidator:[20,3,1,""]}},objnames:{"0":["php","method","PHP method"],"1":["php","attr","PHP attribute"],"2":["php","const","PHP const"],"3":["php","class","PHP class"],"4":["php","interface","PHP interface"]},objtypes:{"0":"php:method","1":"php:attr","2":"php:const","3":"php:class","4":"php:interface"},terms:{"5px":30,"__construct":[3,4,9,13,14,17],"__dir__":[22,28,30,31],"_dir_":30,"abstract":[0,31],"boolean":[],"case":[0,4,13,22,24,25,28,30,32],"char":25,"class":[0,1,3,4,5,7,9,10,11,12,13,14,15,16,17,18,19,20,25,30],"default":[4,5,24,25,26,28,30,31],"float":[],"function":[0,12,13,18,27],"int":[24,25,32],"long":[0,13],"new":[0,1,4,6,13,22,25,28,30,31],"null":[0,1,3,4,13,16,19,24,25,27,28],"public":4,"return":[0,1,2,3,4,6,8,9,10,12,13,16,17,18,19,20,27],"static":1,"switch":[],"throw":[16,26],"true":[0,1,4,13,16,20,22,23,25,27,28],"try":25,"void":[0,8,10,13],"while":28,abbrevi:29,abc:25,abl:29,about:[24,25,27],abov:25,access:[],accord:[9,13,16],achiev:32,action:[27,29],activ:[1,25,28],actual:[0,9,31],add:[0,4,13,22,25,28,31,32],addchild:4,addev:22,addfilt:13,addit:[],addon:[],addpagin:13,addsort:13,adjust:32,admin:[],administr:29,after:[0,1,13,16,27],afterward:16,again:13,against:[9,22],alert:30,all:[0,1,4,9,13,16,17,19,22,24,25,28,29,30,31,32],allow:[25,28],alreadi:30,also:30,alter:25,although:31,alwai:[25,29],amazon:[22,25],amazons3fileprocessor:22,amet:25,amount:[0,4,13,24],ani:[0,13,22],anoth:[0,13,30],anyth:26,api:[21,22],app:[1,16,19,22,25,26,27,28,30,31,32],appear:4,applic:[1,16,19,22,29,30],appropri:[1,28],around:[22,28],arrai:[0,1,4,6,8,9,13,16,20,22,25,28,30,31],arround:3,ascend:[0,4,13,28],assign:0,assum:[24,30],attent:[25,28],author:[23,24,25,28],auto:[],auto_incr:24,automat:22,autor:28,avail:[0,13,16,19,21,24,25,27,31],awar:[0,10,13],back:[22,25],bar:25,base:[17,25],basepath:17,basic:29,befor:[0,13,26,27,30],belong:[0,13,25],besid:24,best:16,between:25,big:28,bigger:28,bigint:25,bit:[26,28],block:[30,32],blue:25,bodi:30,book:[23,24,25,28,30,31,32],book_ibfk_1:25,booklist:32,bool:32,booleanfield:32,boolfield:32,bootstrap:30,bore:28,both:23,bottom:30,box:0,branch:22,broke:[0,13],btn:30,buch:28,build:[0,1,9,24],buildupdata:9,builduplistfilt:1,builduprul:9,button:[25,30,32],call:[0,13,16,30],can:[0,2,13,23,25,27,28,30,32],cancel:27,care:31,cascad:[],caus:1,certain:[16,27,30],chain:[0,13],chang:[28,32],chapter:[22,24,28,29,30,31],charact:25,charset:[25,31],check:[0,4,9,11,13,15,20],checkfieldnam:4,child:4,children:[],childrenlabelfield:[4,25],choic:[24,25],chosen:25,clear:27,click:25,clickabl:25,close:30,closur:[0,13,18,27],code:[1,19,27],color:25,column:[24,32],com:[12,25],combin:[],come:[29,30,31],compos:31,comprehens:25,concret:16,condit:0,configur:[13,16,22,29],configuredefinit:16,connect:[1,22],consetetur:25,constant:[0,13],constraint:[],construct:[2,17],constructor:[3,4,9,13,14,17],contain:[0,4,9,10,13,16,19,28],content:[16,21,23,30,31,32],continu:[24,28],control:[1,2,22,23,24,31],controllercollect:1,controllerproviderinterfac:1,convert:[3,22],cost:26,could:28,count:[0,9],countbi:[0,13],cours:3,creat:[0,1,2,4,6,10,13,16,22,24,25,27,28,29,30,31,32],created_at:[0,4,6,13,24,28],createdata:[2,14],createdefinit:16,createempti:[0,13],createentitydefinit:[5,6],createfil:[0,10,13,17,27],creation:[24,27],cross:25,crud:[1,4,5,6,8,16,21,22,24,25,26,27,28,29,30,31,32],cruddata:1,cruddatafactoryinterfac:28,crudfil:16,crudlexamazons3fileprocessor:[],crudlexentitydefinitionvalidatorinterfac:26,crudlexsampl:24,crudlexus:[],crudmysqldata:28,crudusersetup:22,css:30,current:[0,1,4,6,13,16,19,23,25,28,31],cursor:30,custom:[],cut:25,danger:30,data:[],databas:[2,3,13,14,23,24,27,29,31],datafactori:[16,22,25,28,31],datasourc:[0,13],date:[],datefield:32,datepick:30,datetim:24,datetimefield:32,datetimepick:30,dbal:[13,14],dbname:31,debug:26,decim:25,declar:[4,24,25],defens:[17,25],defin:[0,3,4,13,16,19,23,24,25,27,30,31],definit:23,definitionschema:7,delet:24,deletecascad:[0,4,13,25],deletechildren:[0,13],deleted_at:[4,24,28],deletefil:[0,1,10,13,17,27],deletion_failed_ev:[0,13],deletion_failed_still_referenc:[0,13],deletion_success:[0,13],depend:[3,16],deriv:28,descend:[0,13,28],describ:[21,22,24,25,29],descript:[],desir:[16,19,22,28,30],detail:[1,4,25,28,29],determin:16,differ:25,dir:25,directli:[24,27,32],disabl:28,discuss:31,dismiss:30,displai:[24,25],div:30,doc:22,doctrin:[13,14],doctrineserviceprovid:31,dodelet:[0,13],doesn:[17,25,27,28],dolor:25,don:[4,25,30,32],dot:[25,30],doubl:[19,25],driver:21,dropdown:0,due:[0,13],dure:5,each:[0,13,16,22,24,29,30,32],ead:29,easi:[21,23,29],easili:28,edit:[1,4,25,29,30,32],editpag:28,effect:19,either:[0,1,13,16,23,25],element:[4,25],elet:29,els:[0,3,19,28],email:22,empti:[0,13,19],endblock:30,endfor:30,endif:30,engin:25,enrich:[0,13],enrichentitywithmetadata:[0,13],enrichwithmani:13,enrichwithmanyfield:13,enrichwithrefer:13,entiti:[],entitydefinit:[],entitynam:[0,10,13,17],entiydefinitionfactoryinterfac:5,entri:[0,4,13,16,24,28,29],environ:26,error:[1,9],etc:[22,25,29],even:27,event:[],ever:17,everi:[21,27,28],everyth:20,exact:24,exactli:[0,29],exampl:[0,3,19,23,24,25,27,28,30,31,32],except:[16,26],exclud:[0,4],excludedelet:[0,13],excludeid:[0,13],execut:[0,13,27],exist:[0,4,10,13,16,23,24,32],exlud:4,expect:1,expectedvers:9,extend:[],extens:[12,19],extract:[0,13],factori:[1,14,16],fail:[0,1,9,13,19],fall:25,fals:[0,1,4,13,14,19,22,25,27,28],far:[24,32],featur:[],fetch:13,fetchrefer:[],fetchreferencesforfield:13,few:25,field:23,fieldconstraintstorul:9,fieldlabel:32,fieldnam:4,fieldstructur:6,fieldtypetorul:9,file:[],filefield:32,fileinfo:21,filenam:[16,18],fileprocessor:[0,2,13,14,16,22,25],fileprocessorinterfac:[],filesystem:25,fill:[23,25],filter:[],filteract:1,filteroper:[0,1,13],filtertous:1,find:[12,32],fire:[0,13],firewal:22,first:[0,13,22,24,27,28,30,31],fit:16,fix:[],fixedfield:32,flag:[13,14],flash:[1,30],flashbag:30,flashtyp:30,flashtypeavail:30,flexibl:[6,29],floatfield:32,floatstep:25,folder:30,follow:[22,25,30,31],foo:25,footer:[30,32],forc:25,foreign:[],forget:25,form:25,format:19,formatd:19,formatdatetim:19,formatfloat:19,formattim:19,found:[1,4],from:[25,27],fulfil:0,full:[0,13,25],fullfil:0,further:[25,31],futur:31,gener:[1,13,18,21,22,29,30],generateuuid:13,get:[0,1,3,4,12,13,16,19,22,24,25,27,28,30],getafterdeleteredirectparamet:1,getchildren:4,getchildrenlabelfield:4,getdata:[16,22,27],getdefinit:[0,3,13],geteditablefieldnam:4,getent:16,getfield:4,getfieldlabel:4,getfieldnam:4,getfilt:4,getfilteredfieldnam:4,getformfield:[0,13],getidtonamemap:[0,13],getinitialsortfield:4,getinvaliddetail:[11,15,20],getlabel:4,getlanguagenam:19,getlistfield:4,getlocal:[4,16],getlocalelabel:16,getmanyfield:[0,13],getmanyid:13,getmimetyp:12,getmimetypebyextens:12,getmimetypebyfileinfo:12,getnotfoundpag:1,getpages:4,getpath:17,getpublicfieldnam:4,getraw:3,getreadonlyfield:4,getreferenceid:[0,13],getserviceprovid:4,getstreamedfilefunct:18,getsubtypefield:4,gettabl:4,gettempl:16,getter:[16,22],gettoken:22,gettyp:4,give:[25,32],given:[0,3,4,5,7,8,9,13,16,17,18,19,25,27],global:[],goe:16,gone:25,good:[16,24,29,30,32],got:24,grab:22,green:25,guid:[28,31],had:25,hand:[2,6,22,26,28],handl:[1,10,22,25,27],hard:29,haschildren:13,hash:[22,27],hasmanyset:[0,13],have:[0,13,22,23,24,25,28,29,30,31,32],head:30,header:[0,10,13,30,32],here:[0,10,13,22,24,25,28],hide:24,hierarchi:30,him:22,hint:[],hold:[0,1,3,4,13,14,16,17],host:31,how:[3,12,22,27,28,30],http:[0,1,10,12,13,24,25,31],hydrat:[0,13],i18n:[],idtodata:13,imag:25,implement:[24,25],implicit:4,includ:30,includemani:4,increment:[],index:[21,25],info:12,inform:[24,28],inheritdoc:[5,7,11,13,14,15,17,20],init:16,initchildren:16,initi:[],initialsortascend:[4,28],initialsortfield:[4,28],initlocal:16,initmissingserviceprovid:16,innodb:25,input:[1,3],inset:9,instanc:[0,1,2,4,6,9,13,14,16,22,25,30,31],instanti:22,instead:27,integ:24,integerfield:32,interfac:[2,6,8,10,22,26],intern:[4,22,28,31,32],interrupt:27,intfield:32,introduct:[],invalid:[1,3,4,16],ipsum:25,isdeletecascad:4,isinitialsortascend:4,ismanagingi18n:16,isn:13,isutc:19,isvalid:[11,15,20],isvaliduniqu:20,isvaliduniquemani:20,item:[4,24,25],itself:[30,31],javascript:30,jqueri:30,json:31,just:[0,4,6,10,12,13,23,28,30,32],kei:24,kept:31,known:30,label:[23,24,25],label_:28,label_d:28,lambda:18,languag:19,last:[24,25,27],later:31,latest:[0,13],layer:31,layout:[],lead:25,least:1,let:[22,24],level:[28,30],lib:[23,25],librari:[22,23,24,25,27,28],librarybook:25,librarybook_ibfk_1:25,librarybook_ibfk_2:25,like:[0,3,4,6,10,13,16,19,22,25,27,28,30,31,32],line:25,linebreak:25,link:[24,28,30],list:[25,27],listentri:[0,1,13],listfield:[4,28],listview:[4,28],local:[1,4,6,16,28],locale_fallback:28,localelabel:[4,5,6],localeserviceprovid:28,lock:[1,9,24],log:[],longer:25,longtext:25,look:[3,12,30],lorem:25,lot:29,mail:[22,27],make:[6,25,28],manag:[],managei18n:[16,28],mandatori:23,mani:[],manual:21,manyfield:13,map:[1,9,12,13,16],margin:30,mark:28,master:22,matter:3,maximum:[0,13],mean:25,meant:22,mechan:[24,25],mediumint:25,mediumtext:25,menu:30,messag:24,metadata:[0,13],metayaml:7,method:[1,13],might:[19,22,26,27,28,29],mime:12,mimetyp:[],minim:[24,29,31],miss:16,mix:[0,1,3,4,12,13],mode:1,modif:[1,27],modifi:27,modifyent:1,modifyfilesandsetflashbag:1,moment:[0,13,27,30],more:[22,24,25,27,28,30,31],most:[29,30],mount:[24,31],much:[25,28],multi:25,multilin:[],multilinefield:32,must:[0,13,27],myauthor:28,mybooklayout:30,mycreatebooklayout:30,mycustomvalid:26,myfileprocessor:25,mylayout:30,myownentitydefinitionfactori:28,myshowlayout:30,mysql:24,mysqldata:[],mysqldatafactori:[],name:[0,1,4,10,13,16,17,19,23,24,25,28,29,30,31],namefield:[0,13,22,23,25],navig:24,need:[16,22,23,25,29,30,32],newli:[0,2,10,13],next:[28,29,31],notat:19,note:[23,24,28,32],noth:[4,16],now:[17,24,25,31],number:28,object:[3,27,31],off:[],offer:[1,4,16,22,28,29],often:[28,29],onli:[1,4,9,24,25,28,29,31],oper:[0,1,22],optimist:[1,9,24],optimisticlock:1,option:[0,25,28,31],order:[0,4,13,16,22,25,27,28,30],other:[4,25],otherent:25,othernam:25,our:[23,24,25],output:[0,10,13,18],overrid:25,own:26,packag:[25,28],page:[24,25],pages:[4,28],pagin:[],pair:3,panel:[],param:[0,13],paramet:[25,27],paramsoper:[0,13],parent:25,pars:[8,16,19],part:[22,32],pass:[0,3,9,13],password:[],password_reset:22,passwordreset:22,path:[17,24,25,30],pattern:19,pdate:29,pecl:21,per:[4,23,24,28],perform:[0,9,13,20,22,26],performonfil:[0,13],persist:0,philiplb:[30,31],php:[12,21,25],physic:25,picker:30,place:[24,30],plu:[22,30],point:[0,13,26,29,30,32],pointer:30,pop:[0,13],popev:[0,13,27],popul:[3,22],populateviarequest:3,possibl:[13,25,26,28],post:1,postprocess:1,prefil:[],prefix:30,prepend:30,prepopul:[],present:28,previou:24,primari:[],process:[0,13],processor:[0,2,13,16],product:26,profil:[],project:[22,24,28],proper:25,properti:[0,3,4,9,12,13,14,16,17,30],protect:[0,3,4,9,12,13,14,16,17],provid:[2,4,6,9,16,19,22,26,28,30,31],push:27,pushev:[0,13,27],queri:13,querybuild:13,question:12,quick:[9,29],raw:[0,3,9,13],react:27,read:[0,4,16],readabl:22,readyaml:16,real:25,reat:29,recommend:[22,25,30],red:25,redirect:1,redirectpag:1,refer:23,referenc:[4,13,25,28],referencefield:32,refus:25,regist:[16,19,22,25,26,27,28,30,31],registertwigextens:19,registr:[5,6,28],regular:20,rel:25,relat:[13,25],relationship:25,reli:31,remov:[0,13,25,27],render:[0,1,10,13,30,32],renderfield:32,renderfil:[0,1,10,13,17],replac:25,repres:[3,25],request:[0,1,3,10,13,17],requir:[9,21,22,23,31],reset:[],resolv:30,resourc:1,respons:[0,1,10,13,18],rest:[25,32],result:[0,19],retriev:[0,25],romaricdrigon:7,root:32,rout:1,row:[0,3,13,23,24,28],rule:9,sadipsc:25,sai:[3,24],salt:22,same:[0,4,23,24,29],sampl:[24,25],save:[1,13,25,27],savemani:13,scientif:19,scratch:[],search:[21,28],second:[22,24],section:[16,30,31],secur:22,securityserviceprovid:22,see:[0,2,3,4,9,13,14,16,17,22,24],seiten:28,select:[0,25,32],sens:[25,28],serv:1,servic:[2,4,6,16,26,30],serviceprovid:[],serviceproviderinterfac:16,session:30,set:[],setchildrenlabelfield:4,setdeletecascad:4,setfield:[4,32],setfieldlabel:4,setfilt:4,setinitialsortascend:4,setinitialsortfield:4,setlabel:4,setlistfield:4,setlocal:[1,4,16],setmethod:13,setpages:4,setserviceprovid:4,settabl:4,settyp:4,setup:[25,27,29],setupi18n:1,setuprout:1,setuptempl:1,setvalidationfailedflash:1,setvalu:13,setvaluesandparamet:13,sever:22,shorten:[19,25],should:[0,4,23,26,27,30,31],shouldexecuteev:[0,13],show:[],showlist:1,shown:25,side:25,signatur:[0,13,27],silex:[1,16,21,22,28,29,30,31],silexcontrollercollect:1,simpl:[24,28],simplefilesystemfileprocessor:[],simpli:[17,22,32],singl:[24,25],sit:25,situat:27,size:[0,10,13,25],skip:[0,13],small:[18,24],smallint:25,soft:[0,24],some:[13,22,24,28,29,30],someon:27,someth:27,sometim:28,somewher:28,sort:25,sortascend:[0,13],sortfield:[0,13],sourc:4,space:25,special:28,specif:[0,3,10,13,28,30],specifi:[0,4,32],sql:24,src:[30,32],stackoverflow:12,stand:29,standard:[18,30],standardfieldlabel:[4,5,6],start:[29,30,32],staticfil:1,statu:1,step:25,still:[13,25,29],stop:[0,13],storag:25,store:[1,4,17,25,27,31],stream:[0,10,13,18],string:[0,1,3,4,6,9,10,12,13,16,17,18,19,24,28],structur:[],sub:[0,4,13,22],subchapt:30,subchildren:0,subfold:[17,25],subset:0,subtyp:4,success:[0,13,30],superset:0,support:[23,25,28,31],sure:26,surround:22,symfoni:22,symfonycomponenthttpfoundationredirectrespons:1,symfonycomponenthttpfoundationrespons:10,system:[16,17],tabl:[],tag:[22,30],take:[0,3,13,27,31],tediou:29,templat:[30,31],text:[23,24],textfield:32,than:[25,28],thatfield:[22,25],thatid:[0,13],thei:[0,4,13,25,27,32],them:[13,22],thi:[0,1,2,3,4,9,10,13,16,17,21,22,23,24,25,26,27,28,29,30,31,32],thing:9,think:25,thisfield:[22,25],those:[25,32],though:25,three:[4,25,27],through:31,thx:12,time:[19,24,25,27,31],timestamp:[24,25],timestr:19,timezon:19,tinyint:25,tinytext:25,titel:28,titl:[23,24,25,28],token:22,token_storag:22,told:24,too:[0,10,13],tooltip:[25,30],totyp:3,toward:4,translat:25,translationserviceprovid:28,tri:19,turn:[],tweak:32,twig:[16,19,30,32],twigserviceprovid:30,two:[22,23,24,28],txt:25,type:[23,24],unchang:19,under:24,uniqu:[9,20,22,23],until:25,upcom:13,updat:[0,1,10,13,24,27],update_at:28,updated_at:[0,4,6,13,24,28],updatefil:[0,10,13,17,27],upload:[0,1,2,10,13,21,22,25],url:24,urlfield:32,usag:28,useful:3,user:[],userbas:22,usernam:22,userprovid:[],userrol:22,usersetup:22,useuuid:[13,14],utc:[19,24],utf8:[25,31],util:18,uuid:[],valid:24,validateentitydefinit:[16,26],valu:[23,25],varchar:[25,28],vari:29,variat:2,variou:[24,30],vendor:30,version:[0,4,9,13,22,24,28],via:[6,19,22,25],view:[4,25,28,29,30,32],visit:28,wai:31,want:[2,22,25,26,27,28,30,32],web:[],well:4,were:[4,27],what:[2,3,13,24,27],whatev:31,when:[4,22,24,28,30],whenev:29,where:[1,4,17,24,27,28,29,30],whether:[0,1,4,9,13,14,16,19,20,23],which:[1,4,8,18,19,25,27,31,32],whole:[16,27],within:25,without:25,work:[22,32],would:[24,25,28,30],write:[0,13,29],wrong:[16,26],www:25,yaml:[4,6,16],yet:[16,28],yml:[7,8,16,22,24,25,26,28,31],you:[22,24,25,26,27,28,29,30,31,32],your:[24,25,26,27,29],youraccesskei:22,yourbucket:22,yourcrud:[22,25,28,31],yourdbnam:31,yourdbpassword:31,yourdbus:31,yourhost:31,yoursecretaccesskei:22},titles:["CRUDlex\\AbstractData","CRUDlex\\ControllerProvider","CRUDlex\\DataFactoryInterface","CRUDlex\\Entity","CRUDlex\\EntityDefinition","CRUDlex\\EntityDefinitionFactory","CRUDlex\\EntityDefinitionFactoryInterface","CRUDlex\\EntityDefinitionValidator","CRUDlex\\EntityDefinitionValidatorInterface","CRUDlex\\EntityValidator","CRUDlex\\FileProcessorInterface","CRUDlex\\ManyValidator","CRUDlex\\MimeTypes","CRUDlex\\MySQLData","CRUDlex\\MySQLDataFactory","CRUDlex\\ReferenceValidator","CRUDlex\\ServiceProvider","CRUDlex\\SimpleFilesystemFileProcessor","CRUDlex\\StreamedFileResponse","CRUDlex\\TwigExtensions","CRUDlex\\UniqueValidator","Welcome to CRUDlex&#8217;s documentation!","Addons","Constraints","Data Structure Definition","Data Types","Definition Validation","Events","Extended Features","Introduction","Overriding Layouts","Setup","Overriding Templates"],titleterms:{"boolean":25,"float":25,"switch":28,abstractdata:0,access:22,action:[30,32],addit:32,addon:22,admin:22,auto:28,cascad:25,children:25,combin:28,constraint:23,controllerprovid:1,creation:28,crudlex:[0,1,2,3,4,5,6,7,8,9,10,11,12,13,14,15,16,17,18,19,20,21],crudlexamazons3fileprocessor:22,crudlexus:22,custom:26,data:[22,24,25],datafactoryinterfac:2,date:25,datetim:25,definit:[24,26],delet:25,descript:28,displai:28,document:21,entiti:[3,24,28,30],entitydefinit:[4,28],entitydefinitionfactori:5,entitydefinitionfactoryinterfac:6,entitydefinitionvalid:7,entitydefinitionvalidatorinterfac:8,entityvalid:9,event:27,extend:28,featur:28,field:[24,28,32],file:25,fileprocessorinterfac:10,filter:28,fix:25,foreign:25,form:[28,32],from:30,global:30,hint:25,i18n:28,implement:[26,28],includ:32,increment:28,indic:21,initi:28,instead:28,integ:25,introduct:29,kei:[25,28],label:28,layout:[30,32],list:28,log:22,manag:28,mani:25,manyvalid:11,mimetyp:12,multilin:25,mysql:25,mysqldata:13,mysqldatafactori:14,off:[26,28],overrid:[30,32],own:[28,30],page:[28,32],pagin:28,panel:22,paramet:28,password:22,prefil:[],prepopul:28,primari:28,profil:28,refer:25,referencevalid:15,reset:22,role:22,scratch:30,serviceprovid:16,set:[25,28],setup:31,show:25,simplefilesystemfileprocessor:17,singl:[30,32],sort:28,streamedfilerespons:18,structur:24,tabl:21,templat:32,text:25,translat:28,turn:26,twigextens:19,type:25,uniquevalid:20,url:25,user:22,userprovid:22,uuid:28,valid:26,valu:28,web:28,welcom:21,your:30}})