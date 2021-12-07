<template>
    <form action="">
        <div class="modal-card mx-2">
            <header class="modal-card-head">
                <p class="modal-card-title">
                    Add a new mapping
                </p>
            </header>
            <section class="modal-card-body">
                <b-field
                    label="Webex Device"
                    label-position="on-border"
                    :type="error.device ? 'is-danger' : ''"
                    :message="error.device"
                >
                    <b-select
                        ref="deviceList"
                        v-model="deviceId"
                        placeholder="Select an available shared Webex device"
                        icon="desktop-classic"
                        size="is-medium"
                        :disabled="isDisabled || devices.length === 0"
                        expanded
                        required
                        rounded
                        @input="resetValidateDeviceId()
                                validateDeviceId()"
                        @blur="resetValidateDeviceId()
                               validateDeviceId()"
                    >
                        <optgroup
                            v-for="primarySipUrl in primarySipUrls"
                            :key="primarySipUrl"
                            :label="primarySipUrl"
                        >
                            <option
                                v-for="device in filterDevices(primarySipUrl)"
                                :key="device.serial"
                                :title="device.serial"
                                :value="device.id"
                            >
                                {{ device.serial }}, {{ device.product }}
                            </option>
                        </optgroup>
                    </b-select>
                    <p class="control">
                        <b-button
                            icon-right="refresh"
                            size="is-medium"
                            :disabled="isDisabled || isLoading"
                            :loading="isLoading"
                            rounded
                            @click="listDevices(true)"
                        />
                    </p>
                </b-field>
                <b-field
                    label="Calendar Account"
                    label-position="on-border"
                    :type="error.email ? 'is-danger' : ''"
                    :message="error.email"
                >
                    <b-input
                        v-model="email"
                        type="email"
                        icon="calendar-account"
                        placeholder="john@example.com"
                        size="is-medium"
                        :disabled="isDisabled"
                        required
                        rounded
                        @input="resetValidateEmail()
                                validateEmail()"
                        @blur="resetValidateEmail()
                               validateEmail()"
                    />
                </b-field>
            </section>
            <footer class="modal-card-foot">
                <b-button
                    label="Confirm"
                    type="is-link"
                    size="is-medium"
                    icon-right="check"
                    :loading="isLoading"
                    :disabled="!(deviceId && email) || isLoading"
                    expanded
                    rounded
                    @click="addMapping()"
                />
            </footer>
        </div>
    </form>
</template>

<script>
export default {
    name: 'AddMappingModal',
    props: { canCancel: Boolean },
    data () {
        return {
            devices: [],
            email: null,
            deviceId: null,
            isDisabled: false,
            isLoading: true,
            error: {
                device: null,
                email: null,
                other: null
            }
        }
    },
    computed: {
        primarySipUrls () {
            return Array.from(new Set(this.devices.map(device => device.primarySipUrl))).sort()
        }
    },
    created () {
        this.listDevices()
    },
    methods: {
        resetValidateDeviceId () {

        },
        validateDeviceId () {

        },
        resetValidateEmail () {

        },
        validateEmail () {

        },
        retrieveDevices () {
            window.axios.get('/retrieveDevices')
                .catch(error => {
                    console.error(error)
                    this.error.device = 'Could not retrieve devices.'
                })
                .finally(() => {
                    console.info('Finished retrieving devices on the server.')
                })
        },
        loadDevices () {
            window.axios.get('/devices')
                .then(response => {
                    function getDevices (device) {
                        return {
                            id: device.id,
                            userEmail: device.user_email,
                            placeId: device.place_id,
                            product: device.product,
                            mac: device.mac,
                            serial: device.serial,
                            primarySipUrl: device.primary_sip_url,
                            createdAt: device.created_at,
                            updatedAt: device.updated_at
                        }
                    }

                    this.devices = response.data.map(o => getDevices(o))
                })
                .catch(error => {
                    console.error(error)
                    this.error.device = 'Could not load devices.'
                })
                .finally(() => {
                    console.info('Finished loading device list on client.')
                })
        },
        addMapping () {
            this.isDisabled = true
            this.isLoading = true
            this.error.other = null
            this.error.email = null
            this.error.device = null
            window.axios.post('/addMapping', {
                deviceId: this.deviceId,
                email: this.email
            })
                .then(response => {
                    console.info(response.data)
                    this.$emit('close')
                })
                .catch(error => {
                    console.error(error)
                    this.error.device = 'Could not add device mapping.'
                })
                .finally(() => {
                    this.isDisabled = false
                })
        },
        async listDevices (refresh = false) {
            this.isDisabled = true
            this.isLoading = true
            this.error.device = []
            if (refresh) {
                await this.retrieveDevices()
            }
            await this.loadDevices()
            this.isDisabled = false
            this.isLoading = false
            this.deviceId = null
        },
        filterDevices (primarySipUrl) {
            return this.devices.filter(device => device.userEmail === null &&
                device.primarySipUrl === primarySipUrl)
        }
    }
}
</script>

<style scoped>

</style>
