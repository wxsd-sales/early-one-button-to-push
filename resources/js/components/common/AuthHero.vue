<template>
    <div class="hero is-link">
        <div class="hero-body">
            <h1 class="title is-size-1">
                {{ Location.pathname.toLowerCase() === '/setup' ? 'Setup ' : '' }}{{ appName }}
            </h1>
            <p class="subtitle">
                {{ appDescription }}
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AuthHero',
    computed: {
        appName: function () {
            return document.title.replace(/( \| Webex by Cisco)$/, '')
        },
        appDescription: function () {
            return document.querySelector('head > meta[name=description]').content
        }
    }
}
</script>

<style scoped>

</style>
