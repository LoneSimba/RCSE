import config from '../../api/config';

export default {
    namespaced: true,

    state: {
        language: {},
        langList : []
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
            config.getLangs(langs => {
                commit('setLangList', langs);
            });
        },

        setCurrentLang: function({ state, commit }, lang) {
          commit('setLang', lang);
        },
    },

    mutations: {
        setLang(state, lang) {
            state.language = state.langList.find(value => value.slug === lang) ?? config.getDefaultLang();
        },

        setLangList(state, langs) {
            state.langList = langs;
        }
    }
}
