var customEventManager = {
    active: false,
    afterSend: false,
    container:[],
    add: function(eventKey, eventValue){
        customEventManager.container[customEventManager.container.length] = {
            key: eventKey,
            value: eventValue
        };
    },
    send: function(){
        if(!customEventManager.active && customEventManager.ready() && customEventManager.container.length > 0){
            var index;

            customEventManager.active = true;

            for(index in customEventManager.container){
                aktionid.savecustom(customEventManager.container[index].key, customEventManager.container[index].value);
            }

            customEventManager.container = [];
            customEventManager.active = false;

            if(typeof(customEventManager.afterSend) == 'function'){
                customEventManager.afterSend();
            }
        }
    },
    ready: function(){
        return typeof(aktionid.savecustom) == 'function';
    }
};

setInterval(customEventManager.send, 100);
