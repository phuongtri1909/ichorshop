@foreach ($users as $user)
    <div class="user-item mb-2 col-3">
        <div class="form-check">
            <input class="form-check-input user-checkbox" type="checkbox" name="users[]" value="{{ $user->id }}"
                id="user_{{ $user->id }}"
                {{ isset($selectedUserIds) && in_array($user->id, $selectedUserIds) ? 'checked' : '' }}>
            <label class="form-check-label" for="user_{{ $user->id }}">
                <div class="d-flex align-items-center">
                    <div class="user-name">{{ $user->full_name }}</div>
                    @if ($user->active != \App\Models\User::ACTIVE_YES)
                        <span class="badge bg-warning ms-2">Không hoạt động</span>
                    @endif
                    @if (isset($assignedUsers) && in_array($user->id, $assignedUsers))
                        <span class="badge assigned-badge ms-2">Đã gán</span>
                    @endif
                </div>
                <div class="user-email text-muted">{{ $user->email }}</div>
            </label>
        </div>
    </div>
@endforeach

@push('styles')
    <style>
        .users-container {
            max-height: 350px;
            overflow-y: auto;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 10px;
            position: relative;
        }

        .users-container::-webkit-scrollbar {
            width: 6px;
        }

        .users-container::-webkit-scrollbar-track {
            background: #f1f1f1;
        }

        .users-container::-webkit-scrollbar-thumb {
            background: #c1c1c1;
            border-radius: 3px;
        }

        .users-container::-webkit-scrollbar-thumb:hover {
            background: #a1a1a1;
        }

        .user-item {
            background-color: #fff;
            border: 1px solid #eee;
            border-radius: 4px;
            padding: 8px 12px;
            transition: background-color 0.2s;
        }

        .user-item:hover {
            background-color: #f8f9fa;
        }

        .user-name {
            font-weight: 500;
        }

        .user-email {
            font-size: 0.85rem;
        }
    </style>
@endpush
