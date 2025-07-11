@extends('layouts.app')
@section('title')
    {{ $task->title }} - Task Details
@endsection
@section('content')
    <div class="container">
        <h2 class="mb-4 shadow-sm p-3 rounded bg-white text-center">{{ $task->title }} - Task Details</h2>

        @if (session('success'))
            <div class="alert alert-success">
                {{ session('success') }}
            </div>
        @endif
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card mb-4 shadow-sm">
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-6">
                                <h5 class="card-title">{{ $task->title }}</h5>
                                <p class="card-text">{{ $task->description }}</p>
                                <p class="card-text"><strong>Due Date:</strong> {{ $task->due_date }}</p>
                                <p class="card-text"><strong>Priority:</strong> <span
                                        class="badge {{ $task->priority == 'low' ? 'bg-success' : ($task->priority == 'medium' ? 'bg-warning' : 'bg-danger') }}">{{ ucfirst($task->priority) }}</span>
                                </p>
                                <p class="card-text"><strong>Status:</strong>
                                    @if ($task->status == 'completed')
                                        <span class="badge bg-success">Completed</span>
                                    @elseif($task->status == 'to_do')
                                        <span class="badge bg-primary">To Do</span>
                                    @elseif($task->status == 'in_progress')
                                        <span class="badge bg-warning">In Progress</span>
                                    @endif
                                </p>

                                <p class="card-text"><strong>Assign To:</strong> {{ $task->user->name }}</p>

                                <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                    data-bs-target="#editTaskModal"> <i class="bi bi-pencil-square"></i> </button>
                                <a href="{{ route('projects.tasks.index', $task->project->id) }}" class="btn btn-secondary">
                                    <i class="bi bi-arrow-90deg-left"></i> </a>
                                <!-- Adjust Points Button -->
                                <button type="button" class="btn btn-info" data-bs-toggle="modal"
                                    data-bs-target="#adjustPointsModal">
                                    <i class="bi bi-coin"></i>
                                </button>
                            </div>

                            <div class="col-md-6 border-start">
                                <h5>Time Tracker</h5>
                                <div id="time-tracker">
                                    <span id="time-display">00:00:00</span>
                                    <div>
                                        <button id="start-btn" class="btn btn-success btn-sm"><i
                                                class="bi bi-play-fill"></i></button>
                                        <button id="pause-btn" class="btn btn-warning btn-sm"><i
                                                class="bi bi-pause-fill"></i></button>
                                        <button id="reset-btn" class="btn btn-danger btn-sm"><i
                                                class="bi bi-stop-fill"></i></button>


                                    </div>
                                </div>
                            </div>
                            <div class="col-md-12 mt-3">
                                <div class="d-flex justify-content-between align-items-center border-top pt-2">
                                    <h5>Checklist</h5>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal"
                                        data-bs-target="#addChecklistModal"> <i class="bi bi-plus-circle"></i> </button>
                                </div>

                                <!-- Checklist items -->
                                <ul class="list-group mt-2" id="checklist-items">
                                    @foreach ($task->checklistItems as $item)
                                        <li class="list-group-item d-flex justify-content-between align-items-center"
                                            id="checklist-item-{{ $item->id }}">
                                            <div class="form-check">
                                                <input class="form-check-input" type="checkbox"
                                                    id="checklist-item-checkbox-{{ $item->id }}"
                                                    {{ $item->completed ? 'checked' : '' }}
                                                    onchange="toggleChecklistItem({{ $item->id }})">
                                                <label
                                                    class="form-check-label {{ $item->completed ? 'text-decoration-line-through' : '' }}">{{ $item->name }}</label>
                                            </div>
                                            <div>
                                                <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                                    data-bs-target="#editChecklistModal-{{ $item->id }}"><i
                                                        class="bi bi-pencil-square"></i></button>
                                                <button type="button" class="btn btn-danger btn-sm"
                                                    onclick="deleteChecklistItem({{ $item->id }})"><i
                                                        class="bi bi-trash"></i></button>
                                            </div>
                                        </li>

                                        <!-- Edit Checklist Modal -->
                                    @endforeach
                                </ul>
                                {{-- <div class="modal fade" id="editChecklistModal-{{ $item->id }}" tabindex="-1"
                                        aria-labelledby="editChecklistModalLabel-{{ $item->id }}"
                                        aria-hidden="true">
                                        <div class="modal-dialog">
                                            <div class="modal-content">
                                                <form id="edit-checklist-form-{{ $item->id }}"
                                                    action="{{ route('checklist-items.update', $item->id) }}"
                                                    method="POST">
                                                    @csrf
                                                    @method('PUT')
                                                    <div class="modal-header">
                                                        <h5 class="modal-title"
                                                            id="editChecklistModalLabel-{{ $item->id }}">Edit
                                                            Checklist Item</h5>
                                                        <button type="button" class="btn-close" data-bs-dismiss="modal"
                                                            aria-label="Close"></button>
                                                    </div>
                                                    <div class="modal-body">
                                                        <div class="mb-3">
                                                            <label for="checklist-name-{{ $item->id }}"
                                                                class="form-label">Item Name</label>
                                                            <input type="text" name="name"
                                                                id="checklist-name-{{ $item->id }}"
                                                                class="form-control" value="{{ $item->name }}"
                                                                required>
                                                            <div class="invalid-feedback"
                                                                id="checklist-name-error-{{ $item->id }}"></div>
                                                        </div>
                                                    </div>
                                                    <div class="modal-footer">
                                                        <button type="button" class="btn btn-secondary"
                                                            data-bs-dismiss="modal">Close</button>
                                                        <button type="submit" class="btn btn-primary">Update
                                                            Item</button>
                                                    </div>
                                                </form>
                                            </div>
                                        </div>
                                    </div> --}}
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Adjust Points Modal -->
        <div class="modal fade" id="adjustPointsModal" tabindex="-1" aria-labelledby="adjustPointsModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="adjust-points-form" action="{{ route('tasks.adjustPoints', $task->id) }}" method="POST">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="adjustPointsModalLabel">Adjust Task Points</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>

                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="points" class="form-label">Points</label>
                                <input type="number" name="points" id="points" class="form-control"
                                    min="1" required>
                            </div>

                            <div class="mb-3">
                                <label for="adjust_type" class="form-label">Action</label>
                                <select name="adjust_type" id="adjust_type" class="form-select" required>
                                    <option value="increment">Increment</option>
                                    <option value="decrement">Decrement</option>
                                </select>
                            </div>

                            <div class="mb-3">
                                <label for="reason" class="form-label">Reason</label>
                                <textarea name="reason" id="reason" class="form-control" rows="3" required></textarea>
                            </div>
                        </div>

                        <div class="modal-footer">
                            <button type="reset" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Adjust</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>


        <!-- Add Checklist Modal -->
        <div class="modal fade" id="addChecklistModal" tabindex="-1" aria-labelledby="addChecklistModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form id="add-checklist-form">
                        @csrf
                        <div class="modal-header">
                            <h5 class="modal-title" id="addChecklistModalLabel">Add Checklist Item</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="checklist-name" class="form-label">Item Name</label>
                                <input type="text" name="name" id="checklist-name" class="form-control" required>
                                <div class="invalid-feedback" id="checklist-name-error"></div>
                            </div>
                            <input type="hidden" name="task_id" id="task_id" value="{{ $task->id }}">
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Add Item</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Edit Task Modal -->
        <div class="modal fade" id="editTaskModal" tabindex="-1" aria-labelledby="editTaskModalLabel"
            aria-hidden="true">
            <div class="modal-dialog">
                <div class="modal-content">
                    <form action="{{ route('tasks.update', $task->id) }}" method="POST">
                        @csrf
                        @method('PUT')
                        <div class="modal-header">
                            <h5 class="modal-title" id="editTaskModalLabel">Edit Task</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal"
                                aria-label="Close"></button>
                        </div>
                        <div class="modal-body">
                            <div class="mb-3">
                                <label for="title" class="form-label">Title</label>
                                <input type="text" name="title" id="title" class="form-control"
                                    value="{{ $task->title }}" required>
                                @error('title')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="description" class="form-label">Description</label>
                                <textarea name="description" id="description" class="form-control">{{ $task->description }}</textarea>
                                @error('description')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="due_date" class="form-label">Due Date</label>
                                <input type="date" name="due_date" id="due_date" class="form-control"
                                    value="{{ $task->due_date }}">
                                @error('due_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="expected_completion_date" class="form-label">Expected Date</label>
                                <input type="date" name="expected_completion_date" id="expected_completion_date"
                                    class="form-control" value="{{ $task->expected_completion_date }}">
                                @error('expected_completion_date')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="priority" class="form-label">Priority</label>
                                <select name="priority" id="priority" class="form-select" required>
                                    <option value="low" {{ $task->priority == 'low' ? 'selected' : '' }}>Low</option>
                                    <option value="medium" {{ $task->priority == 'medium' ? 'selected' : '' }}>Medium
                                    </option>
                                    <option value="high" {{ $task->priority == 'high' ? 'selected' : '' }}>High</option>
                                </select>
                                @error('priority')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                            <div class="mb-3">
                                <label for="task_status_id" class="form-label">Status</label>
                                <select name="task_status_id" id="task_status_id" class="form-select" required>
                                    @foreach ($statuses as $status)
                                        <option value="{{ $status->id }}"
                                            {{ $task->taskStatus->id == $status->id ? 'selected' : '' }}>
                                            {{ $status->name }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('task_status_id')
                                    <span class="text-danger">{{ $message }}</span>
                                @enderror
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                            <button type="submit" class="btn btn-primary">Update Task</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script>
        let timer;
        let seconds = 0;
        let isRunning = false;

        function formatTime(sec) {
            let hours = Math.floor(sec / 3600);
            let minutes = Math.floor((sec % 3600) / 60);
            let seconds = sec % 60;

            return `${hours.toString().padStart(2, '0')}:${minutes.toString().padStart(2, '0')}:${seconds.toString().padStart(2, '0')}`;
        }

        function updateTimeDisplay() {
            document.getElementById('time-display').innerText = formatTime(seconds);
        }

        document.getElementById('start-btn').addEventListener('click', () => {
            if (!isRunning) {
                isRunning = true;
                timer = setInterval(() => {
                    seconds++;
                    updateTimeDisplay();
                }, 1000);
            }
        });

        document.getElementById('pause-btn').addEventListener('click', () => {
            if (isRunning) {
                isRunning = false;
                clearInterval(timer);
            }
        });

        document.getElementById('reset-btn').addEventListener('click', () => {
            isRunning = false;
            clearInterval(timer);
            seconds = 0;
            updateTimeDisplay();
        });

        updateTimeDisplay();

        function toggleChecklistItem(itemId) {
            const url = '{{ route('checklist-items.update-status', ':id') }}'.replace(':id', itemId);
            const checkbox = document.getElementById(`checklist-item-checkbox-${itemId}`);
            fetch(url, {
                    method: 'GET',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const label = checkbox.closest('.form-check').querySelector('.form-check-label');
                        label.classList.toggle('text-decoration-line-through', checkbox.checked);
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // function toggleChecklistItem(itemId) {
        //     const checkbox = document.getElementById(`checklist-item-checkbox-${itemId}`);
        //     const form = document.getElementById(`edit-checklist-form-${itemId}`);
        //     const formData = new FormData(form);
        //     formData.append('completed', checkbox.checked ? '1' : '0');

        //     fetch(form.action, {
        //         method: 'POST',
        //         headers: {
        //             'X-CSRF-TOKEN': '{{ csrf_token() }}'
        //         },
        //         body: formData
        //     })
        //     .then(response => response.json())
        //     .then(data => {
        //         if (data.success) {
        //             const itemElement = checkbox.closest('li');
        //             const label = checkbox.nextElementSibling;
        //             label.classList.toggle('text-decoration-line-through', checkbox.checked);
        //         }
        //     })
        //     .catch(error => console.error('Error:', error));
        // }

        function deleteChecklistItem(itemId) {
            const form = document.getElementById(`delete-checklist-form-${itemId}`);
            const formData = new FormData(form);

            fetch(form.action, {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        document.getElementById(`checklist-item-${itemId}`).remove();
                    }
                })
                .catch(error => console.error('Error:', error));
        }

        // AJAX for adding checklist item
        document.getElementById('add-checklist-form').addEventListener('submit', function(e) {
            e.preventDefault();

            const form = this;
            const formData = new FormData(form);

            fetch('{{ route('checklist-items.store', $task->id) }}', {
                    method: 'POST',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: formData
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        console.log(data)
                        const checklistItem = document.createElement('li');
                        checklistItem.className =
                            'list-group-item d-flex justify-content-between align-items-center';
                        checklistItem.id = `checklist-item-${data.id}`;
                        checklistItem.innerHTML = `
                        <div class="form-check">
                            <input class="form-check-input" type="checkbox" id="checklist-item-checkbox-${data.id}"
                                onchange="toggleChecklistItem(${data.id})">
                            <label class="form-check-label">${data.name}</label>
                        </div>
                        <div>
                            <button type="button" class="btn btn-primary btn-sm" data-bs-toggle="modal"
                                data-bs-target="#editChecklistModal-${data.id}"><i class="bi bi-pencil-square"></i></button>
                            <button type="button" class="btn btn-danger btn-sm" onclick="deleteChecklistItem(${data.id})"><i class="bi bi-trash"></i></button>
                        </div>
                    `;

                        document.getElementById('checklist-items').appendChild(checklistItem);
                        form.reset();
                        document.querySelector('#addChecklistModal .btn-close').click();
                    } else {
                        const errorElement = document.getElementById('checklist-name-error');
                        errorElement.textContent = data.message;
                        errorElement.style.display = 'block';
                    }
                })
                .catch(error => console.error('Error:', error));
        });
    </script>
@endsection
