var customEventManager = {
    container:[],
    active: false,
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
        }
    },
    ready: function(){
        return typeof(aktionid.savecustom) == 'function';
    }
};

setInterval(customEventManager.send, 100);
