export const PREFERENCES = {
    initData    :   function(){
        if(sessionStorage.getItem('PREFERENCES') === null || sessionStorage.getItem('PREFERENCES') === undefined) {
            sessionStorage.setItem('PREFERENCES', true);
            sessionStorage.setItem('PREFERENCES_company', null);
        } // if(sessionStorage.getItem('PREFERENCES') === null || sessionStorage.getItem('PREFERENCES') === undefined) { ... }
    },
    retrieveData:   function(){
        if(sessionStorage.getItem('PREFERENCES') === null) LAYOUT.initData();

        var vgbPreferences   =   {
            'PREFERENCES'           :   sessionStorage.getItem('PREFERENCES'),
            'PREFERENCES_company'   :   sessionStorage.getItem('PREFERENCES_company'),
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
    }
}
