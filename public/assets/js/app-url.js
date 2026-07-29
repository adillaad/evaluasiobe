(function () {
    window.appUrl = function (path) {
        var base = window.appBaseUrl || '';
        path = path || '';
        if (path.charAt(0) !== '/') {
            path = '/' + path;
        }
        return base + path;
    };
})();
