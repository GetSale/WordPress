(function (w, c) {
    w[c] = w[c] || [];
    w[c].push(function (inTarget) {
        inTarget.event('del-from-cart');
        console.log('del-from-cart');
    });
})(window, 'inTargetCallbacks');