<template>
    <v-layout row wrap class="fill-height">
        <v-flex v-for="(tab, n) in tabs" class="text-xs-center ml-1 pt-0 lead-tab" 
            :class="!!is_selected(tab)?'zalfa-bg-purple white--text active':'grey lighten-2'" 
            :key="n" @click="select(tab)">
            {{tab.text}}
        </v-flex>
    </v-layout>
</template>
<style scoped>
.lead-tab {
    align-items: center;
    justify-content: center;
    display: flex;
    text-transform: uppercase;
}

.lead-tab.active {
    font-weight: bold;
}
</style>
<script>
module.exports = {
    components : {
    },

    data () {
        return {
            
        }
    },

    computed : {
        tabs () {
            return this.$store.state.tabs
        },

        selected_tab : {
            get () { return this.$store.state.selected_tab },
            set (v) { this.$store.commit('set_selected_tab', v) }
        }
    },

    methods : {
        is_selected (v) {
            if (!this.selected_tab) return false
            if (v.id == this.selected_tab.id)
                return true
            return false
        },

        select (v) {
            this.$store.commit('set_selected_tab', v)    
        }
    },

    mounted () {
        this.$store.commit('set_selected_tab', this.$store.state.tabs[0])
    }
}