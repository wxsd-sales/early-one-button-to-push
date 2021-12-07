<template>
    <div class="hero is-link">
        <div class="hero-body">
            <h1 class="title is-size-1">
                {{ $route.path.toLowerCase() === '/setup' ? 'Setup ' : '' }}Early One Button to Push
            </h1>
            <p class="subtitle">
                Customize the timing of OBTP on your shared Webex devices.
            </p>
        </div>
    </div>
</template>

<script>
export default {
    name: 'AuthHero'
}
</script>

<style scoped>

</style>
