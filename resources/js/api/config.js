// TODO remove mocks
const _langs = [
    { 'id': 1, 'slug': 'ru' },
    { 'id': 2, 'slug': 'en' },
    { 'id': 3, 'slug': 'jp' }
];

const _menu = [
    { 'id': 1, 'slug': 'home', 'url': '/', 'icon': 'home_silhouette_32' },
    { 'id': 2, 'slug': 'feed', 'url': '/feed', 'icon': 'newspaper_silhouette_32' },
    { 'id': 3, 'slug': 'forum', 'url': '/forum', 'icon': 'conversations_silhouette_32' }
];

export default {
    getLangList(cb) {
        setTimeout(() => {
            return cb(_langs);
        }, 100);
    },

    getDefaultLang() {
        return _langs[0];
    },

    setLang(lang, cb, errorCb) {
        setTimeout(() => cb(), 100)
    },

    getMenuList(cb) {
        setTimeout( () => {
            return cb(_menu);
        }, 100);
    }
}
