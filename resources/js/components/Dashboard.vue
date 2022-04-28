<template>
    <section>
        <auth-hero />
        <div class="columns is-centered is-multiline is-mobile my-1 py-6">
            <div class="column is-four-fifths">
                <div class="columns is-vcentered">
                    <div class="column is-three-fifths has-text-centered-mobile is-subtitle">
                        <p>{{ syncStatus }}</p>
                    </div>
                    <div class="column is-two-fifths">
                        <b-button
                            :loading="isSyncButtonLoading && !isTableLoading"
                            :disabled="devices.length === 0 || isTableLoading"
                            size="is-medium"
                            label="Sync Now"
                            type="is-link"
                            class="is-rounded"
                            icon-right="sync"
                            expanded
                            @click="performSync()"
                        />
                    </div>
                </div>
            </div>
            <div class="column is-four-fifths is-hidden-mobile">
                <b-field
                    class="column is-full"
                    position="is-right"
                    group-multiline
                    grouped
                >
                    <div
                        v-for="(column, index) in columnsVisible"
                        :key="index"
                        class="control"
                    >
                        <b-checkbox
                            v-model="column.isVisible"
                            :disabled="isTableLoading"
                            type="is-link"
                        >
                            {{ column.label }}
                        </b-checkbox>
                    </div>
                </b-field>
            </div>
            <div class="column is-four-fifths">
                <b-table
                    :data="devices"
                    :default-sort="[columnsVisible.primarySipUrl.field]"
                    :loading="isTableLoading"
                    :height="600"
                    hoverable
                    sticky-header
                >
                    <b-table-column
                        v-slot="props"
                        :field="columnsVisible.id.field"
                        :label="columnsVisible.id.label"
                        :visible="columnsVisible.id.isVisible"
                        searchable
                        sortable
                        width="160"
                    >
                        <div :title="props.row.id">
                            {{ props.row.id.slice(-7) }}
                        </div>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        :field="columnsVisible.userEmail.field"
                        :label="columnsVisible.userEmail.label"
                        :visible="columnsVisible.userEmail.isVisible"
                        searchable
                        sortable
                    >
                        <a
                            class="is-wrap-anywhere"
                            :href="'mailto:' + props.row.userEmail"
                        >{{ props.row.userEmail }}</a>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        :field="columnsVisible.placeId.field"
                        :label="columnsVisible.placeId.label"
                        :visible="columnsVisible.placeId.isVisible"
                        searchable
                        sortable
                    >
                        <div :title="props.row.placeId">
                            {{ props.row.placeId.slice(-7) }}
                        </div>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        :field="columnsVisible.product.field"
                        :label="columnsVisible.product.label"
                        :visible="columnsVisible.product.isVisible"
                        searchable
                        sortable
                    >
                        {{ props.row.product }}
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        :field="columnsVisible.mac.field"
                        :label="columnsVisible.mac.label"
                        :visible="columnsVisible.mac.isVisible"
                        searchable
                        sortable
                    >
                        <code>
                            {{ props.row.mac }}
                        </code>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        :field="columnsVisible.serial.field"
                        :label="columnsVisible.serial.label"
                        :visible="columnsVisible.serial.isVisible"
                        searchable
                        sortable
                        width="100"
                    >
                        <code>
                            {{ props.row.serial }}
                        </code>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        :field="columnsVisible.primarySipUrl.field"
                        :label="columnsVisible.primarySipUrl.label"
                        :visible="columnsVisible.primarySipUrl.isVisible"
                        searchable
                        sortable
                    >
                        <a
                            class="is-wrap-anywhere"
                            :href="'webexteams://meet?sip=' + props.row.primarySipUrl"
                        >{{ props.row.primarySipUrl }}</a>
                    </b-table-column>
                    <b-table-column
                        v-slot="props"
                        label="Actions"
                        width="120"
                        centered
                    >
                        <b-button
                            v-if="props.row.userEmail"
                            icon-right="link-off"
                            size="is-small"
                            type="is-danger is-light"
                            class="is-rounded"
                            @click="postRemoveMapping(props.row)"
                        />
                        <b-button
                            v-else
                            icon-right="link-plus"
                            size="is-small"
                            type="is-link is-light"
                            class="is-rounded"
                            expanded
                            @click="showAddMappingModal(props.row)"
                        />
                        <b-tooltip
                            v-if="props.row.userEmail"
                            type="is-light"
                            position="is-left"
                            multilined
                        >
                            <template #content>
                                {{ 'last synced on ' + new Date(props.row.syncedAt) }}
                            </template>
                            <b-button
                                icon-right="sync"
                                size="is-small"
                                type="is-link is-light"
                                class="is-rounded"
                                @click="performSync(props.row)"
                            />
                        </b-tooltip>
                    </b-table-column>
                </b-table>
            </div>
            <div class="column is-four-fifths">
                <b-button
                    label="Add Mapping"
                    type="is-link"
                    size="is-medium"
                    class="is-rounded"
                    icon-right="link-plus"
                    expanded
                    @click="showAddMappingModal()"
                />
            </div>
        </div>
    </section>
</template>

<script>
import AddMappingModal from './common/AddMappingModal'

export default {
    name: 'Dashboard',
    data () {
        return {
            devices: [],
            hideUnmappedDevices: false,
            columnsVisible: {
                id: {
                    label: 'Device Id',
                    field: 'id',
                    isVisible: true
                },
                userEmail: {
                    label: 'Calender Account',
                    field: 'userEmail',
                    isVisible: true
                },
                placeId: {
                    label: 'Place Id',
                    field: 'placeId',
                    isVisible: false
                },
                product: {
                    label: 'Product',
                    field: 'product',
                    isVisible: true
                },
                mac: {
                    label: 'MAC',
                    field: 'mac',
                    isVisible: false
                },
                serial: {
                    label: 'Serial',
                    field: 'serial',
                    isVisible: true
                },
                primarySipUrl: {
                    label: 'Primary SIP URL',
                    field: 'primarySipUrl',
                    isVisible: true
                }
            },
            isTableLoading: true,
            isSyncButtonLoading: false,
            syncStatus: ''
        }
    },
    created () {
        this.loadTable()
    },
    methods: {
        showAddMappingModal (deviceRow = null) {
            this.$buefy.modal.open({
                parent: this,
                props: {
                    selectedDeviceId: deviceRow ? deviceRow.id : null
                },
                component: AddMappingModal,
                hasModalCard: true,
                trapFocus: true,
                canCancel: true,
                scroll: 'keep',
                events: {
                    close: (userEmail) => {
                        if (userEmail) {
                            this.$buefy.toast.open({
                                duration: 5000,
                                message: `Processed mapping for ${userEmail}`,
                                position: 'is-top',
                                type: 'is-success'
                            })

                            if (deviceRow) {
                                deviceRow.userEmail = userEmail
                            } else {
                                this.loadTable()
                            }
                        } else {
                            deviceRow.userEmail = null
                            // TODO: Fix Initial table load
                        }
                    }
                }
            })
        },
        loadTable () {
            this.isTableLoading = true
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
                            syncedAt: device.synced_at,
                            createdAt: device.created_at,
                            updatedAt: device.updated_at
                        }
                    }

                    this.devices = response.data.map(o => getDevices(o))
                })
                .catch(error => {
                    console.error(error)
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: `${error}. You may retry after sometime.`,
                        position: 'is-top',
                        type: 'is-danger'
                    })
                })
                .finally(() => {
                    this.isTableLoading = false
                })
        },
        postRemoveMapping (row) {
            this.isTableLoading = true
            window.axios.post('/removeMapping', {
                deviceId: row.id,
                email: row.userEmail
            })
                .then(response => {
                    console.info(response)
                    row.userEmail = null
                })
                .catch(error => {
                    console.error(error)
                    this.$buefy.toast.open({
                        duration: 5000,
                        message: `${error}. You may retry after sometime.`,
                        position: 'is-top',
                        type: 'is-danger'
                    })
                })
                .finally(() => {
                    this.isTableLoading = false
                })
        },
        async performSync (row) {
            this.isSyncButtonLoading = true
            this.isTableLoading = true

            const meetings = await window.axios.get('/retrieveMeetings', {
                params: {
                    email: row ? row.userEmail : null
                }
            })

            const bookings = await window.axios.get('/performBookingsPut', {
                params: {
                    email: row ? row.userEmail : null
                }
            })

            if (meetings.status === 200 && bookings.status === 200) {
                const message = `Synced Bookings for ${row ? row.userEmail : 'all users'}.`
                this.$buefy.toast.open({
                    duration: 5000,
                    message: message,
                    position: 'is-top',
                    type: 'is-success'
                })
            } else {
                this.$buefy.toast.open({
                    duration: 5000,
                    message: 'Request failed. You may retry after sometime.',
                    position: 'is-top',
                    type: 'is-danger'
                })
            }

            this.isSyncButtonLoading = false
            this.isTableLoading = false
        }
    }
}
</script>

<style scoped>
.is-wrap-anywhere{
    overflow-wrap: anywhere;
}
</style>
