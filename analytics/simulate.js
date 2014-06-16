var utils = require('utils');

function next() {
    this.echo(this.getTitle());
    if (Math.random() < .7) {
        var links = this.evaluate(function() {
            return Array.prototype.map.call(document.querySelectorAll('a'), function(e) {
                return e.id;
            });
        });
        var link = links[Math.floor(Math.random() * links.length)];
        if (link) {
            this.then(next);
            this.click('#' + link);
        }
    }
}

var casper = require('casper');
casper
    .create()
    .start('http://abtronic.gpirujo.com/~gpirujo/abtronic/analytics/?pagina=A', next)
    .run()
;
