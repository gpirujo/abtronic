var prefix = 'http://abtronic.gpirujo.com/~gpirujo/abtronic/ejemplo/servidor/';
var start = prefix + 'index.php?p=listado';
var follow = 8;

function random(to) {
    return Math.floor(Math.random() * to);
}

function choose(options) {
    var total = 0;
    for (var i in options) {
        total += options[i];
    }
    var choice = random(total);
    var cummulative = 0;
    for (var i in options) {
        cummulative += options[i];
        if (cummulative > choice) {
            return i;
        }
    }
}

function browse(response) {

    console.log(this.getTitle());

    if (random(follow)) {

        var links = this.evaluate(function() {
            var links = {};
            var elements = document.getElementsByTagName('a');
            for (var i = 0; i < elements.length; i++) {
                links[elements[i].getAttribute('href')] = parseInt(elements[i].getAttribute('rel'));
            }
            return links;
        });

        var choice = choose(links);

        this.thenOpen(prefix + choice).then(browse);

    }

}

require('casper').create().start().open(start).then(browse).run();
