((function() {
    if (typeof this.RokGallery == 'undefined') this.RokGallery = {};

    var loves = this.RokGallery.Loves = {

        actions: {love: 'add', unlove: 'remove'},

        scan: function() {
            var toLove = document.id(document.body).getElements('.action-love'),
                toUnlove = document.id(document.body).getElements('.action-unlove');

            $$(toLove, toUnlove).each(function(element, i) {
                var type = element.hasClass('action-love') ? 'love' : 'unlove';
                element.store('loveID', loves.getID(element));
                element.store('loveAction', type);
                element.store('loveCount', loves.getCount(element));
                element.store('loveText', loves.getText(element));
                element.store('loveRequest', loves.buildRequest(element, type));

                element.addEvent('click', loves.send.bind(loves, element));
            }, this);
        },

        getID: function(element) {
            var classes = element.getProperty('class'), id = null;
            classes = classes.clean().trim();

            id = classes.match(/id-([0-9]{1,})/i);

            if (id.length) id = id[1].toInt();

            return id;
        },

        getCount: function(element) {
            var counters = document.id(document.body).getElements('.rg-item-loves-counter.id-' + element.retrieve('loveID'));

            return counters;
        },

        getText: function(element) {
            return document.id(document.body).getElement('.action-text.id-' + element.retrieve('loveID'));
        },

        buildRequest: function(element, action) {
            var id = element.retrieve('loveID'),
                data = {};

            data[RokGallery.ajaxVars.model] = 'loves';
            data[RokGallery.ajaxVars.action] = loves.actions[action];
            data.params = JSON.encode({id: id});

            return new Request.JSON({
                url: RokGallery.url || '',
                onRequest: function() {
                    loves.onRequest(element);
                },
                onSuccess: function(response) {
                    loves.onSuccess(element, response);
                },
                data: data
            });
        },

        validate: function(string) {
            string = string.replace(/\\(?:["\\\/bfnrt]|u[0-9a-fA-F]{4})/g, '@').
                replace(/"[^"\\\n\r]*"|true|false|null|-?\d+(?:\.\d*)?(?:[eE][+\-]?\d+)?/g, ']').
                replace(/(?:^|:|,)(?:\s*\[)+/g, '');

            return (/^[\],:{}\s]*$/).test(string);
        },

        send: function(element) {
            var request = element.retrieve('loveRequest'),
                id = element.retrieve('loveID'),
                action = element.retrieve('loveAction'),
                data = {};
            if (request.running) return;

            data[RokGallery.ajaxVars.model] = 'loves';
            data[RokGallery.ajaxVars.action] = loves.actions[action];
            data.params = JSON.encode({id: id});

            request.send({
                data: data
            });
        },

        onRequest: function(element) {
            element.addClass('loading');
        },

        onSuccess: function(element, response) {
            element.removeClass('loading');

            var json = (typeof loves == 'string') ? loves.validate(response) : true;

            if (!json) throw new Error('Invalid JSON response.');
            else {
                if (response.status == 'error') {
                    throw new Error(response.message);
                    return;
                }

                var count = response.payload.loves,
                    text = response.payload.text,
                    action = response.payload.new_action;

                element.retrieve('loveCount').set('html', count);
                element.removeClass('action-love').removeClass('action-unlove');
                element.addClass('action-' + action).store('loveAction', action);
                if (element.retrieve('loveText')) element.retrieve('loveText').set('html', text);
            }

        }
    };

    window.addEvent('domready', loves.scan);

})());
