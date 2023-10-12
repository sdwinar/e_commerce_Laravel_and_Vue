<script setup>
import { formatDate } from '../../helper.js';
import { ref } from 'vue';
import { useToastr } from '../../toastr.js';
import axios from 'axios';

const toastr = useToastr();

const props = defineProps({
    user: Object,
    index: Number,
    selectAll: Boolean,
});

const emit = defineEmits(['userDeleted']);

// const roles = ref([
//     {
//         name: 'ADMIN',
//         value: 1
//     },
//     {
//         name: 'USER',
//         value: 2,
//     }
// ]);

// const changeRole = (user, role) => {
//     axios.patch(`/api/users/${user.id}/change-role`, {
//         role: role,
//     })
//     .then(() => {
//         toastr.success('Role changed successfully!');
//     })
// };

// const toggleSelection = () => {
//     emit('toggleSelection', props.user);
// }
</script>
<template>
  <tr class="text-center">
    <th scope="row">{{ index + 1 }}</th>
    <td>{{ user.name }}</td>
    <td>{{ user.email }}</td>
    <td>{{ convertDateToCharacterMonth(user.created_at) }}</td>
    <td>{{ user.role }}</td>

    <td>
      <a href="#" @click.prevent="editUser(user)"
        ><i class="fa fa-edit"></i>
      </a>

      <a href="#" @click.prevent="confirmUserDeletion(user)"
        ><i class="fa fa-trash ml-3 text-danger" aria-hidden="true"></i>
      </a>
    </td>
  </tr>

  <!-- Modal -->
  <div class="modal fade" id="deleteUserModal">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
          <button
            type="button"
            class="close"
            data-dismiss="modal"
            aria-label="Close"
          >
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body">
          <p>Are you sure you want to delete?</p>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-dismiss="modal">
            No
          </button>
          <button
            @click.prevent="deleteUser"
            type="button"
            class="btn btn-danger"
          >
            Yes
          </button>
        </div>
      </div>
    </div>
  </div>
</template>
