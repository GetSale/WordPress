(function (w, c) {
    w[c] = w[c] || [];
    w[c].push(function (inTarget) {
        inTarget.event('add-to-cart');
        console.log('add-to-cart');
    });
})(window, 'inTargetCallbacks');