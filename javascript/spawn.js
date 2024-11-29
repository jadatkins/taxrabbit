function spawn(id) {
  document.getElementById(id).style.position = "static";
  document.getElementById(id).style.visibility = "visible";
}

function despawn(id) {
  document.getElementById(id).style.visibility = "hidden";
  document.getElementById(id).style.position = "absolute";
}

function newjob() {
  spawn("job_cage");
  despawn("job_spawn");
  despawn("expense_spawn");
  return false;
}

function newexpense() {
  spawn("expense_cage");
  despawn("job_spawn");
  despawn("expense_spawn");
  return false;
}

function unjob() {
  spawn("job_spawn");
  spawn("expense_spawn");
  despawn("job_cage");
}

function unexpense() {
  spawn("job_spawn");
  spawn("expense_spawn");
  despawn("expense_cage");
}