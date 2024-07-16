<template>
    <v-dialog
        v-model="dialog"
        scrollable
        :overlay="false"
        max-width="500px"
        transition="dialog-transition"
    >
        <v-card>
            <v-card-title primary-title class="cyan white--text pt-3">
                <h3>EXPENSE BARU</h3>
            </v-card-title>
            <v-card-text>
                <v-layout row wrap>
                    <v-flex xs12>
                        <v-layout row wrap>

                            <v-flex xs12>
                                <v-text-field
                                    label="Kode Level"
                                    v-model="level_code"
                                    readonly
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs12>
                                <v-text-field
                                    label="Nama Level"
                                    v-model="level_name"
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs6 pr-2>
                                <v-text-field
                                    label="Omzet Bulanan Min"
                                    v-model="level_monthly_min"
                                    reverse
                                    number
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs6 pl-2>
                                <v-text-field
                                    label="Max"
                                    v-model="level_monthly_max"
                                    reverse
                                    number
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs6 pr-2>
                                <v-text-field
                                    label="Omzet 3 Bulanan Min"
                                    v-model="level_3month_min"
                                    reverse
                                    number
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs6 pl-2>
                                <v-text-field
                                    label="Max"
                                    v-model="level_3month_max"
                                    reverse
                                    number
                                ></v-text-field>
                            </v-flex>

                            <v-flex xs6 pr-2>
                                <v-select
                                    :items="levels"
                                    return-object
                                    item-value="level_id"
                                    item-text="level_name"
                                    v-model="level_downgrade"
                                    label="Level Downgrade"
                                    clearable
                                ></v-select>
                            </v-flex>

                            <v-flex xs6 pl-2>
                                <v-select
                                    :items="levels"
                                    return-object
                                    item-value="level_id"
                                    item-text="level_name"
                                    v-model="level_upgrade"
                                    label="Level Upgrade"
                                    clearable
                                ></v-select>
                            </v-flex>

                        </v-layout>
                    </v-flex>
                </v-layout>
            </v-card-text>

            <v-card-actions>
                <v-btn color="primary" flat @click="dialog=!dialog">Batal</v-btn>
                <v-spacer></v-spacer>
                <v-btn color="primary" @click="save">Simpan</v-btn>                
            </v-card-actions>
        </v-card>
    </v-dialog>
</template>

<style>
.input-dense .v-input__control {
    min-height: 36px !important;
}
</style>
<script>
module.exports = {
    components : {
        'common-datepicker' : httpVueLoader('../../common/components/common-datepicker.vue')
    },

    data () {
        return { }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.level_new.dialog_new },
            set (v) { this.$store.commit('level_new/set_common', ['dialog_new', v]) }
        },

        level_name : {
            get () { return this.$store.state.level_new.level_name },
            set (v) { this.$store.commit('level_new/set_common', ['level_name', v]) }
        },

        level_code : {
            get () { return this.$store.state.level_new.level_code },
            set (v) { this.$store.commit('level_new/set_common', ['level_code', v]) }
        },

        levels () {
            return this.$store.state.level.levels
        },

        level_monthly_min : {
            get () { return this.$store.state.level_new.level_monthly_min },
            set (v) { this.$store.commit('level_new/set_common', ['level_monthly_min', v]) }
        },

        level_monthly_max : {
            get () { return this.$store.state.level_new.level_monthly_max },
            set (v) { this.$store.commit('level_new/set_common', ['level_monthly_max', v]) }
        },

        level_3month_min : {
            get () { return this.$store.state.level_new.level_3month_min },
            set (v) { this.$store.commit('level_new/set_common', ['level_3month_min', v]) }
        },

        level_3month_max : {
            get () { return this.$store.state.level_new.level_3month_max },
            set (v) { this.$store.commit('level_new/set_common', ['level_3month_max', v]) }
        },

        level_upgrade : {
            get () { return this.$store.state.level_new.level_upgrade },
            set (v) { this.$store.commit('level_new/set_level_upgrade', v) }
        },

        level_downgrade : {
            get () { return this.$store.state.level_new.level_downgrade },
            set (v) { this.$store.commit('level_new/set_level_downgrade', v) }
        }
    },

    methods : {
        save () {
            this.$store.dispatch('level_new/save')
        }
    },

    mounted () {},

    watch : {}
}
</script>