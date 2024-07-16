<template>
  <div class="text-xs-center">
    <v-menu offset-y min-width="100%" style="" v-model="dialog" position-y="0">
      <!-- <template v-slot:activator="{ on }">
        <v-btn
          color="primary"
          dark
          v-on="on"
        >
          Dropdown
        </v-btn>
      </template> -->
      <v-layout row wrap>
          <v-flex xs10 sm6 offset-xs1 offset-sm3>
            <v-list>
        <v-list-tile
          v-for="(item, index) in items"
          :key="index"
          @click=""
        >
          <v-list-tile-title>{{ item.title }}</v-list-tile-title>
        </v-list-tile>
      </v-list>        
          </v-flex>
      </v-layout>
      
    </v-menu>
  </div>
</template>

<script>
  module.exports = {
    data () { return {
      items: [
        { title: 'Click Me' },
        { title: 'Click Me' },
        { title: 'Click Me' },
        { title: 'Click Me 2' }
      ]
    }
    },

    computed : {
        dialog : {
            get () { return this.$store.state.salesorder.dialog_action },
            set (v) { this.$store.commit('salesorder/set_common', ['dialog_action', v]) }
        }
    }
  }
</script>