<template>
    <v-card>
        <v-card-text class="pa-1">
            <v-tabs
                v-model="tab"
                color="indigo lighten-3"
                grow
                >
                <v-tabs-slider color="yellow"></v-tabs-slider>

                <v-tab
                    v-for="item in tab_titles"
                    :key="item"
                >
                    <v-layout column>
                        <v-flex><b>{{ tab_title(item)[0] }}</b></v-flex>
                        <v-flex class="caption" v-if="tab_title(item)[1]">{{ tab_title(item)[1] }}</v-flex>
                    </v-layout>
                </v-tab>
            </v-tabs>

            <v-tabs-items v-model="tab">
                <v-tab-item>
                    <crm-post-sales-filter></crm-post-sales-filter>
                </v-tab-item>
                <v-tab-item>
                    <crm-post-sales-profiles></crm-post-sales-profiles>
                </v-tab-item>
            </v-tabs-items>
        </v-card-text>
    </v-card>
</template>
<style scoped>
.rounded {
    border-radius: 10px;
}
.btn-item-delete {
    flex-grow: 0;
}
</style>
<script>
module.exports = {
    components: {
        "crm-post-sales-filter" : httpVueLoader("./crm-post-sales-filter.vue?t=<?=date('YmdHis');?>"),
        "crm-post-sales-profiles" : httpVueLoader("./crm-post-sales-profiles.vue?t=<?=date('YmdHis');?>")
      },

    data () {
        return {
            // tab: null,
            tab_titles: [
                'Filter', 'Profil'
            ],
            text: 'Lorem ipsum mbuh'
        }
    },

    computed : {
        tab : {
            get () { return this.$store.state.post_sales.selected_main_tab },
            set (v) { this.$store.commit('post_sales/set_object', ['selected_main_tab', v]) }
        }
    },

    methods: {
        tab_title(x) {
            let e = x.split('|')            
            if (!!e[1]) e.push("")
            return e
        }
    },

    mounted () {
        this.$store.dispatch('post_sales/search_profile')
    }
}
</script>