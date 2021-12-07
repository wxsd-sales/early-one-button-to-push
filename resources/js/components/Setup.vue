<template>
    <section>
        <auth-hero />
        <div class="columns is-centered is-mobile my-1 py-6">
            <div class="column is-two-thirds">
                <!-- Azure -->
                <div class="column">
                    <h2 class="is-size-2">
                        Azure OAuth
                    </h2>
                    <p class="subtitle">
                        Authenticate with Microsoft Azure.
                    </p>
                    <p class="block">
                        Click the button to authorize this application on Azure.
                    </p>
                    <p
                        v-if="error['email']"
                        class="block has-text-danger"
                    >
                        {{ error['email'][0] }} Kindly retry.
                    </p>
                    <div class="columns">
                        <div class="column">
                            <b-field
                                v-if="azure"
                                label="Azure User Id"
                                label-position="on-border"
                                custom-class="is-large"
                                type="is-success"
                            >
                                <b-input
                                    size="is-large"
                                    type="password"
                                    disabled
                                    readonly
                                    :value="azure"
                                />
                            </b-field>
                            <a
                                v-else
                                class="button is-large is-fullwidth is-link is-rounded"
                                :href="url['azure']"
                            >
                                <img
                                    :src="'/images/microsoft.svg'"
                                    width="40"
                                    alt=""
                                >
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import AuthHero from './common/AuthHero'

export default {
    name: 'Setup',
    components: {
        AuthHero
    },
    props: {
        csrf: {
            type: String,
            required: true
        },
        azure: {
            type: String,
            default: null
        },
        error: {
            type: Object,
            required: true
        },
        url: {
            type: Object,
            required: true
        }
    }
}
</script>

<style scoped>
</style>
