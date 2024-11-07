const { updateTaskStatus } = require('./taskManager');

global.fetch = jest.fn(() =>
  Promise.resolve({
    text: () => Promise.resolve('Status updated'),
  })
);

describe('updateTaskStatus', () => {
  it('should send the correct task id and status to the server', () => {
    const taskId = 1;
    const status = 'true';

    updateTaskStatus(taskId, status);

    expect(fetch).toHaveBeenCalledWith('index.php', expect.objectContaining({
      method: 'POST',
      body: expect.stringContaining(`task_id=${taskId}&status=${status}`),
    }));
  });
});
