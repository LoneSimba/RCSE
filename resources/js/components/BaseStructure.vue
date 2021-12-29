<template>
    <div id="page">
        <BaseHeader></BaseHeader>

        <BaseWrapper></BaseWrapper>
    </div>
</template>

<script>
import {mapActions, mapGetters, mapState} from "vuex";

import BaseHeader from "./Structure/BaseHeader";
import BaseWrapper from "./Structure/BaseWrapper";

export default {
    name: "BaseStructure",

    components: {BaseWrapper, BaseHeader},

    computed: mapState('config', {
        language: state => state.language,
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

<style lang="scss" scoped>

#page {
    display: flex;
    align-items: center;
    flex-direction: column;
}
</style>
