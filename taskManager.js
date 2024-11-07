function updateTaskStatus(taskId, status) {
  return fetch('index.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
    },
    body: `task_id=${taskId}&status=${status}`,
  })
    .then((response) => response.text())
    .then((data) => console.log('Status updated:', data))
    .catch((error) => console.error('Error:', error));
}

module.exports = { updateTaskStatus };
