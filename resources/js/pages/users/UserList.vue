<script setup>
import axios from "axios";
import { ref, onMounted, reactive } from "vue";
import { Form, Field, useResetForm } from "vee-validate";
import * as yup from "yup";

const users = ref([]);
const editing = ref(false);
const formValues = ref(); // formValues.value = {
const form = ref(null); //form.value.resetForm();

// ***********************************************************

const getUsers = () => {
  axios.get("/api/users").then((response) => {
    users.value = response.data;
    // alert(users.value)
  });
};

// ***********************************************************

const createUserSchema = yup.object({
  name: yup.string().required(),
  email: yup.string().email().required(),
  password: yup.string().required().min(4),
});

// ***********************************************************

const editUserSchema = yup.object({
  name: yup.string().required(),
  email: yup.string().email().required(),
  password: yup.string().when((password, schema) => {
    return password ? schema.min(4) : schema;
  }),
});

// ***********************************************************

const createUser = (values) => {
  axios.post("/api/users", values).then((response) => {
    users.value.unshift(response.data);

    $("#addUserModel").modal("hide");
    // resetForm();
  });
};

// ***********************************************************

const addUser = () => {
  editing.value = false;
  form.value.resetForm();
// ***********************************************************
}
const editUser = (user) => {
  editing.value = true;
  form.value.resetForm();
  $("#addUserModel").modal("show");
  formValues.value = {
    id: user.id,
    name: user.name,
    email: user.email,
    password: user.password,
  };
};

// ***************************************************************

const updateUser = (values) => {

  axios.put("/api/users/" + formValues.value.id, values)
    .then((response) => {
      const index = users.value.findIndex(
        (user) => user.id === response.data.id
      );
      users.value[index] = response.data;
      form.value.resetForm();
      $("#addUserModel").modal("hide");

      // alert(user.id)
      //   toastr.success("User updated successfully!");
    })
    .catch((error) => {
      alert(error);
    })
    .finally(() => {
      //form.value.resetForm();

    });
};

// *******************************************************************

const handleSubmit = (values, actions) => {
  // console.log(actions);
  if (editing.value) {
    updateUser(values);
  } else {
    createUser(values);
  }
};

//******************************************************************** */
onMounted(() => {
  getUsers();
});
</script>
<template>
  <div class="content-header">
    <div class="container-fluid">
      <div class="row mb-2">
        <div class="col-sm-6">
          <h1 class="m-0">USERS</h1>
        </div>
        <div class="col-sm-6">
          <ol class="breadcrumb float-sm-right">
            <li class="breadcrumb-item">
              <router-link to="/admin">Home</router-link>
            </li>
            <li class="breadcrumb-item active">Starter Page</li>
          </ol>
        </div>
      </div>
    </div>
  </div>
  <div class="content">
    <div class="container-fluid">
      <div class="row">
        <button
          type="button"
          class="btn btn-primary mb-2"
          data-toggle="modal"
          data-target="#addUserModel"
          @click="addUser"
        >
          Add New User
        </button>
      </div>
      <div class="row">
        <table class="table table-bordered">
          <thead>
            <tr>
              <th scope="col">#</th>
              <th scope="col">name</th>
              <th scope="col">email</th>
              <th scope="col">Handle</th>
            </tr>
          </thead>
          <tbody v-if="users.value != 0">
            <tr v-for="(user, index) in users" :key="user.id">
              <th scope="row">{{ index + 1 }}</th>
              <td>{{ user.name }}</td>
              <td>{{ user.email }}</td>
              <td>
                <a href="#" @click.prevent="editUser(user)"
                  ><i class="fa fa-edit"></i
                ></a>
              </td>
            </tr>
            <tr></tr>
          </tbody>
          <tbody v-else>
            <tr class="text-center">
              No Data
            </tr>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Modal -->
  <div
    class="modal fade"
    id="addUserModel"
    data-backdrop="static"
    tabindex="-1"
    role="dialog"
    aria-labelledby="staticBackdropLabel"
    aria-hidden="true"
  >
    <div class="modal-dialog" role="document">
      <Form
        ref="form"
        autocomplete="off"
        @submit="handleSubmit"
        :validation-schema="editing ? editUserSchema : createUserSchema"
        v-slot="{ errors }"
        :initial-values="formValues"
      >
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="staticBackdropLabel">
              <span v-if="editing">Edit User</span>
              <span v-else>Add New User</span>
            </h5>
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
            <div class="form-group">
              <label for="name">Name</label>
              <Field
                :class="{ 'is-invalid': errors.name }"
                name="name"
                type="text"
                class="form-control"
                id="name"
                aria-describedby="nameHelp"
                placeholder="Enter full name"
              />
              <span class="invalid-feedback">{{ errors.name }}</span>
            </div>

            <div class="form-group">
              <label for="email">Email</label>
              <Field
                :class="{ 'is-invalid': errors.email }"
                name="email"
                type="email"
                class="form-control"
                id="email"
                aria-describedby="nameHelp"
                placeholder="Enter full name"
              />
              <span class="invalid-feedback">{{ errors.email }}</span>
            </div>

            <div class="form-group">
              <label for="password">Password</label>
              <Field
                :class="{ 'is-invalid': errors.password }"
                name="password"
                type="password"
                class="form-control"
                id="password"
                aria-describedby="nameHelp"
                placeholder="Enter password"
              />
              <span class="invalid-feedback">{{ errors.password }}</span>
            </div>
          </div>
          <div class="modal-footer">
            <button
              type="button"
              class="btn btn-secondary"
              data-dismiss="modal"
            >
              Cancel
            </button>
            <button type="submit" class="btn btn-primary">
              {{ editing ? "Edit" : "Save" }}
            </button>
          </div>
        </div>
      </Form>
    </div>
  </div>
</template>
