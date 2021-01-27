window.local_quickregister = {
    randomKey: function (length) {
        var result = '';
        var characters = 'ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz0123456789';
        var charactersLength = characters.length;
        for (var i = 0; i < length; i++) {
            result += characters.charAt(Math.floor(Math.random() * charactersLength));
        }
        return result;
    },
    generateRandomKey: function (length, selector) {
        document.querySelector(selector).value = this.randomKey(length);
    },
    copyToClipboard: function (selector) {
        var copyText = document.querySelector(selector);

        copyText.select();
        copyText.setSelectionRange(0, 99999);

        document.execCommand('copy');
    }
};