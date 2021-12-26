// TODO remove mock
const _langs = [
    { 'id': 1, 'slug': 'ru' },
    { 'id': 2, 'slug': 'en' },
    { 'id': 3, 'slug': 'jp' }
];

export default {
    getLangs(cb) {
        setTimeout(() => {
            return cb(_langs);
        }, 100);
    },

    getDefaultLang() {
        return _langs[0];
    },

    setLang(lang, cb, errorCb) {
        setTimeout(() => cb(), 100)
    }
}
