var Video = (function($) {
    var INTERVAL_SECONDS = 5,
        ONE_SECOND = 1000;

    var Video = function(size) {
        var self = this;

        if (size === 0) {
            throw new Error("Video size must be greater than 0");
        }

        this.size = size;

        /**
         * Get percentage of video timing for seconds
         *
         * @param {number} seconds
         * @returns {number}
         */
        this.getPercentage = function(seconds) {
            return seconds / (this.size / 100);
        };

        /**
         * Send request about viewing
         *
         * @param {number} seconds
         */
        this.report = function(seconds) {
            var data = {
                type: Video.TYPE_PROGRESS,
                payload: ''
            };

            if (seconds === 0) {
                data.type = Video.TYPE_LOADED;
            } else if (seconds >= this.size) {
                data.type = Video.TYPE_FINISHED;
            } else {
                data.payload = this.getPercentage(seconds);
            }

            $.ajax({
                method: 'POST',
                url: '/stat',
                dataType: 'json',
                data: data
            });
        };

        /**
         * Make step of video viewing each INTERVAL_SECONDS
         *
         * @param {number} elapsed
         */
        this.step = function(elapsed) {
            self.report(elapsed);

            if (elapsed < self.size) {
                setTimeout(self.step, ONE_SECOND * INTERVAL_SECONDS, elapsed + INTERVAL_SECONDS);
            }
        };

        /**
         * Start video viewing
         */
        this.start = function() {
            this.step(0);
        };
    };

    $.extend(Video, {
        TYPE_LOADED: 'loaded',
        TYPE_PROGRESS: 'progress',
        TYPE_FINISHED: 'finished'
    });

    return Video;
})(jQuery);

$(function() {
    var video1 = new Video(20);

    video1.start();
});
