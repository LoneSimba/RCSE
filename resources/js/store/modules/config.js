import config from '../../api/config';

export default {
    namespaced: true,

    state: {
        language: {},
        langList : [],
        mainMenu: []
    },

    getters: {
        getCurrentLang: function(state) {
            return state.language;
        },

        getDefaultLang: function() {
            return config.getDefaultLang();
        },
    },

    actions: {
        getAvailableLangs: function({ commit }) {
            config.getLangList(langs => {
                commit('setLangList', langs);
            });
        },

        setCurrentLang: function({ state, commit }, lang) {
          commit('setLang', lang);
        },

        getMainMenu: function ({ commit }) {
            config.getMenuList(menu => {
                commit('setMainMenu', menu);
            })
        }
    },

    mutations: {
        setLang(state, lang) {
            state.language = state.langList.find(value => value.slug === lang) ?? config.getDefaultLang();
        },

        setLangList(state, items) {
            state.langList = items;
        },

        setMainMenu(state, items) {
            state.mainMenu = items;
        }
    }
}
