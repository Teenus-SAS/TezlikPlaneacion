fetch(`/api/dashboardCountsGeneral`)
  .then((response) => response.text())
  .then((data) => {
    data = JSON.parse(data);
    generalProductsCounts(data.allProducts);
    generalCompaniesCounts(data.allCompanies);
    generalUsersCounts(data.allUsers);
    generalUsersSessionCounts(data.allUsersSession);
  });

/* Colors */
// dynamicColors = () => {
//   let letters = '0123456789ABCDEF'.split('');
//   let color = '#';

//   for (var i = 0; i < 6; i++) color += letters[Math.floor(Math.random() * 16)];
//   return color;
// };

// getRandomColor = (a) => {
//   let color = [];
//   for (i = 0; i < a; i++) color.push(dynamicColors());
//   return color;
// };

/* Cantidad de Productos */
generalProductsCounts = (data) => {
  $('#products').html(data.products.toLocaleString('es-ES'));
}

/* Cantidad de Empresas */
generalCompaniesCounts = (data) => {
  $('#companies').html(data.companies.toLocaleString('es-ES'));
}

/* Cantidad de usuarios */
generalUsersCounts = (data) => {
  $('#users').html(data.users.toLocaleString('es-ES'));
}

/* Cantidad de usuarios en sesión */
generalUsersSessionCounts = (data) => {
  $('#usersSession').html(data.users_session.toLocaleString('es-ES'));
}
