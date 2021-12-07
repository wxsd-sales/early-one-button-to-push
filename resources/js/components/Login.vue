<template>
    <section>
        <auth-hero />
        <div class="columns is-centered is-mobile my-1 py-6">
            <div class="column is-two-thirds">
                <!-- Email -->
                <div class="columns is-mobile mb-4 pb-4">
                    <div class="column">
                        <h2 class="is-size-2">
                            Your Login Token
                        </h2>
                        <p class="subtitle">
                            Provide the login token sent to you on Webex.
                        </p>
                        <form
                            class="columns"
                            method="POST"
                            :action="url['login']"
                        >
                            <input
                                type="hidden"
                                name="_token"
                                :value="csrf"
                            >
                            <div class="column">
                                <b-field
                                    label="Token"
                                    label-position="on-border"
                                    custom-class="is-large"
                                    :type="error['email'] ? 'is-danger': token ? 'is-success' : ''"
                                    :message="error['email'] ? error['email'][0] + ' Kindly retry.' : ''"
                                >
                                    <b-input
                                        size="is-large"
                                        icon="key"
                                        name="token"
                                        type="password"
                                        custom-class="is-rounded"
                                        :value="token"
                                        required
                                    />
                                </b-field>
                            </div>
                            <div
                                v-if="!token || error['token']"
                                class="column is-4"
                            >
                                <b-button
                                    type="is-link"
                                    native-type="submit"
                                    size="is-large"
                                    icon-right="chevron-right"
                                    class="is-rounded"
                                    expanded
                                >
                                    Login
                                </b-button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>

<script>
import AuthHero from './common/AuthHero'

export default {
    name: 'Login',
    components: {
        AuthHero
    },
    props: {
        csrf: {
            type: String,
            required: true
        },
        token: {
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
