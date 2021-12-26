<template>
    <BaseHeader :language="language" :langList="langList"></BaseHeader>
</template>

<script>
import {mapActions, mapGetters, mapState} from "vuex";

import BaseHeader from "./Structure/BaseHeader";

export default {
    name: "BaseStructure",

    components: {BaseHeader},

    computed: mapState('config', {
        language: state => state.language,
        langList: state => state.langList,
    }),

    methods: {
        ...mapActions('config', [
            'setCurrentLang'
        ]),
        ...mapGetters('config', [
            'getDefaultLang'
        ])
    },

    beforeCreate() {
        this.$store.dispatch('config/getAvailableLangs')

    },

    created() {
        this.setCurrentLang(navigator.language);
    }
}
</script>

<style scoped>

</style>
