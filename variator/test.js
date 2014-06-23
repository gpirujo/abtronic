var utils = require('utils');

var casper = require('casper').create();
casper.start();

var totals = {};
for (var i = 0; i < 1000; i++) {
    casper.thenOpen('http://192.168.56.101/~gpirujo/abtronic/variator/', function(response) {
        var results = this.evaluate(function() {
            var results = {};
            var elements = document.getElementsByClassName('result');
            for (var i = 0; i < elements.length; i++) {
                results[elements[i].id] = elements[i].innerText;
            }
            return results;
        });
        Object.keys(results).forEach(function(id) {
            if (!totals[id]) totals[id] = {};
            var text = results[id];
            if (!totals[id][text]) totals[id][text] = 0;
            totals[id][text]++;
        });
    });
}
casper.then(function() {
    utils.dump(totals);
});

casper.run();
