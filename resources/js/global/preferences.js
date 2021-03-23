export const PREFERENCES = {
    initData    :   function(){
        if(sessionStorage.getItem('PREFERENCES') === null || sessionStorage.getItem('PREFERENCES') === undefined) {
            sessionStorage.setItem('PREFERENCES', true);
            sessionStorage.setItem('PREFERENCES_company', null);
            sessionStorage.setItem('PREFERENCES_proccess', null);
            sessionStorage.setItem('PREFERENCES_type', null);
        } // if(sessionStorage.getItem('PREFERENCES') === null || sessionStorage.getItem('PREFERENCES') === undefined) { ... }
    },
    retrieveData:   function(){
        if(sessionStorage.getItem('PREFERENCES') === null) LAYOUT.initData();

        var vgbPreferences   =   {
            'PREFERENCES'           :   sessionStorage.getItem('PREFERENCES'),
            'PREFERENCES_company'   :   sessionStorage.getItem('PREFERENCES_company'),
            'PREFERENCES_proccess'  :   sessionStorage.getItem('PREFERENCES_proccess'),
            'PREFERENCES_type'      :   sessionStorage.getItem('PREFERENCES_type'),
        };

        return vgbPreferences;
    }, // retrieveData:   function(){ ... });
    setCompany  :   function(idCompany) {
        if(idCompany == "null") {
            sessionStorage.setItem('PREFERENCES_company', null);
        }
        else {
            sessionStorage.setItem('PREFERENCES_company', idCompany);
        }
    },
    getCompany  :   function() {
        var returnData  =   sessionStorage.getItem('PREFERENCES_company');
        if(returnData === "null") {
            return null;
        }

        return returnData;
    }, // getCompany  :   function() {  ... }
    setProccess  :   function(idProccess) {
        if(idProccess == "null") {
            sessionStorage.setItem('PREFERENCES_proccess', null);
        }
        else {
            sessionStorage.setItem('PREFERENCES_proccess', idProccess);
        }
    },
    getProccess  :   function() {
        var returnData  =   sessionStorage.getItem('PREFERENCES_proccess');
        if(returnData === "null") {
            return null;
        }

        return returnData;
    }, // getCompany  :   function() {  ... }
    
    setType  :   function(idProccess) {
        if(idProccess == "null") {
            sessionStorage.setItem('PREFERENCES_type', null);
        }
        else {
            sessionStorage.setItem('PREFERENCES_type', idProccess);
        }
    },
    getType  :   function() {
        var returnData  =   sessionStorage.getItem('PREFERENCES_type');
        if(returnData === "null") {
            return null;
        }

        return returnData;
    } // getCompany  :   function() {  ... }
}
