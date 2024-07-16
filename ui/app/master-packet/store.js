// State
//  data ...
// Mutations
//
//
// Actions
import packet from "./modules/packet.js?t=14ssdss3";
import packet_new from "./modules/packet_new.js?t=asd";

import packetdetail from "./modules/packetdetail.js?t=1234";
import packet_sale from "./modules/packet_sale.js";
import master_level from "../master-customer-level/modules/level.js"
import system from "../assets/js/system.js";

export const store = new Vuex.Store({
    state: {
        dialog_delete: false,
        snackbar: false,
        snackbar_text: 'Data telah tersimpan'
    },

    mutations: {
        set_dialog_delete(state, v) {
            state.dialog_delete = v
        },

        set_snackbar(state, v) {
            state.snackbar = v
        },

        set_snackbar_text(state, v) {
            state.snackbar_text = v
        }
    },

    modules: {
        packet: packet,
        packet_new: packet_new,
        packetdetail: packetdetail,
        packet_sale: packet_sale,
        master_level: master_level,
        system: system
    }
});