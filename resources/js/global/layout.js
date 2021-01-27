export const LAYOUT = {
    initData    :   function(){
        if(sessionStorage.getItem('LAYOUT') === null || sessionStorage.getItem('LAYOUT') === undefined) {
            sessionStorage.setItem('LAYOUT', true);
            sessionStorage.setItem('LAYOUT_version', '3.0.0');
            sessionStorage.setItem('LAYOUT_expansible', false);
            sessionStorage.setItem('LAYOUT_alerts', []);
        } // if(sessionStorage.getItem('LAYOUT') === null || sessionStorage.getItem('LAYOUT') === undefined) { ... }
    }, // initData    :   function(){ ... }
    retrieveData:   function(){
        if(sessionStorage.getItem('LAYOUT') === null) LAYOUT.initData();

        var vgbLayout   =   {
            'LAYOUT'            :   sessionStorage.getItem('LAYOUT'),
            'LAYOUT_version'    :   sessionStorage.getItem('LAYOUT_version'),
            'LAYOUT_expansible' :   sessionStorage.getItem('LAYOUT_expansible'),
            'LAYOUT_alerts'     :   sessionStorage.getItem('LAYOUT_alerts'),
        };

        return vgbLayout;
    }, // retrieveData:   function(){ ... }
}
