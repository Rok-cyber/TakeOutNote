 
/** 
* Author    : MC 심새 (ganer9r@naver.com) 
* Make Date : 2006-09-22 
* comment    : ajax의 xml을 javascript 객체형식으로 자동 변환 
**/ 

/* Ajax 사용을 위한 기본 콤포넌트 Start */ 
function AjaxDataControl(){ 
    this.xmlHttp            = null; 

    this.getHttpRequest        = function(URL, object){ 
        var xmlHttp        = this.xmlHttp; 
        var xmlData        = null; 
        // FF일 경우 window.XMLHttpRequest 객체가 존재한다. 
        if(!xmlHttp){ 
            if(window.XMLHttpRequest) { 
                xmlHttp    = new XMLHttpRequest(); 
            } else { 
                xmlHttp    = new ActiveXObject("Microsoft.XMLHTTP"); 
            } 
            this.xmlHttp    = xmlHttp; 
        } 

        xmlHttp.open('GET', URL, true); 		
        xmlHttp.onreadystatechange = function() { 
            // readyState 가 4 고 status 가 200 일 경우 올바르게 가져옴 
            if(xmlHttp.readyState==4 && xmlHttp.status == 200 && xmlHttp.statusText=='OK') { 
                var xmlData        = xmlHttp.responseXML; 
                object.setXmlData(xmlData); 

            } 
        } 


        xmlHttp.send(''); 

    } 

    this.getXmlRootNode        = function(nodes, rootName){ 
        var rootNode    = nodes.getElementsByTagName(rootName); 

        return rootNode; 
    } 
} 
/* Ajax 사용을 위한 기본 콤포넌트 End */ 




/* Ajax에서 리턴받은 XML NODE를 JAVASCRIPT OBJECT 형식으로 변환 Start */ 
function AjaxObject(){ 
    this.ac                = null; 
    this.xmlData        = null; 
    this.rootName        = null; 
    this.functionName    = null; 
    this.arguments        = new Array(); 

    this.inArray            = function(array, value){ 
        var result    = false; 

        for(var i=0; i < array.length; i++){ 
            if(array[i] == value){ 
                result    = true; 
                break; 
            } 
        } 

        return result; 
    } 


    this.getHttpRequest        = function(Url, functionName){ 
        if(this.ac == null){ 
            this.ac            = new AjaxDataControl(); 
        } 
        this.functionName    = functionName; 

        for(var i=2; i <arguments.length; i++){ 
            this.arguments.push(arguments[i]); 
        } 

        this.ac.getHttpRequest(Url, this); 
    } 




    this.setMakeObject        = function(nodeData){ 
        var resultObject    = new Object; 
        resultObject.length    = 0; 

        if(nodeData.hasChildNodes() ){ 
            var nodeChilds        = nodeData.childNodes; 
            var nodeNameList    = new Array(); 
            var isNodeChilds    = false; 

            for(var i = 0; i<nodeChilds.length;i++){ 

                if(nodeChilds[i].nodeType == '1'){ 

                    var returnObj    = this.setMakeObject(nodeChilds[i] ); 

                    if( typeof(returnObj) == "string"){ 
                        resultObject[ nodeChilds[i].nodeName ]    = this.setMakeObject(nodeChilds[i] ); 
                        resultObject.length    += 1; 
                    }else{ 

                        if(resultObject[ nodeChilds[i].nodeName ]){ 
                            resultObject[ nodeChilds[i].nodeName ].push( returnObj ); 
                        }else{ 
                            resultObject[ nodeChilds[i].nodeName ]    = new Array(); 
                            resultObject[ nodeChilds[i].nodeName ].push( returnObj ); 

                            resultObject.length    += 1; 
                        } 
                    } 
                    isNodeChilds    = true; 

                } 
            } 

            if(!isNodeChilds){ 
                resultObject    = nodeChilds[0].nodeValue; 
            }else{ 

            } 
        } 

        return resultObject; 
    } 


    this.setMakeControl        = function(){ 
        if(this.xmlData != null){ 
            var rootNode    = this.ac.getXmlRootNode(this.xmlData, this.rootName); 
            var list        = this.setMakeObject(rootNode[0]); 
            var    args        = ""; 

            for(var i=0; i<this.arguments.length; i++){ 
                args    += ", this.arguments["+i+"]"; 
            } 

            eval( this.functionName+"(list "+args+")" ); 
        } 
    } 

    this.setXmlData            = function(data){ 
        this.xmlData		= data; 
        this.rootName       = data.documentElement.nodeName; 

        this.setMakeControl(); 
    } 

} 
/* Ajax에서 리턴받은 XML NODE를 JAVASCRIPT OBJECT 형식으로 변환 End */ 