<template>
    <div id="main-wrapper">
        <BaseSidebar></BaseSidebar>
<!--    main contetns    -->
    </div>
</template>

<script>
import BaseSidebar from "./BaseSidebar";

export default {
    name: "BaseWrapper",

    components: {BaseSidebar}
}
</script>

<style lang="scss" scoped>

#main-wrapper {
    width: 928px;
    display: flex;
    margin-top: 48px;
    min-height: 512px;
    flex-direction: row;

}
</style>
