<div x-data="{ showEdit: false, showReply: false }" class="card bg-light comment {{ $isReply ? 'reply mb-2 me-2' : 'mb-3' }}"
    id="comment-{{ $comment->id }}">

    <h5 class="card-header">
        <span class="row">
            <span class="col-md-6 text-dark avatar">
                <img src="{{ asset('images/avatars/' . ($comment->user->avatar ?? 'default.png')) }}"
                    alt="{{ $comment->user->first_name ?? 'Deleted user' }} {{ $comment->user->last_name ?? '' }}"
                    class="rounded-circle me-1">

                @if ($comment->user)
                    {{ $comment->user->first_name }} {{ $comment->user->last_name }} says:
                @else
                    Deleted user says:
                @endif
            </span>

            <span class="col-md-6 text-dark d-none d-md-flex align-items-center justify-content-end">
                {{ date('jS M Y', strtotime($comment->created_at)) }}
            </span>
        </span>
    </h5>

    <div class="card-body bg-white p-2">
        <p class="comment__text">{{ $comment->body }}</p>

        {{-- Actions --}}
        @if (Auth::check() && $comment->user)
            <ul class="comment-actions list-unstyled">
                @if ($comment->user->id === Auth::id())
                    {{-- Edit + Delete for your own comments --}}
                    <li>
                        <a class="comment-edit" @click.prevent="showEdit = !showEdit">
                            <i class="fa-regular fa-pen-to-square"></i>
                            <span x-text="showEdit ? 'Cancel' : 'Edit'"></span>
                        </a>
                    </li>
                    <li>
                        @include('themes/' . $theme_directory . '/partials/comment-delete-form', [
                            'commentOrReply' => $comment,
                        ])
                    </li>
                @elseif (!$isReply)
                    {{-- Reply link only on top-level comments --}}
                    <li>
                        <a class="comment-reply" @click.prevent="showReply = !showReply">
                            <i class="fa-regular fa-comments"></i>
                            <span x-text="showReply ? 'Cancel' : 'Reply'"></span>
                        </a>
                    </li>
                @endif
            </ul>
        @endif

        {{-- Edit form for comment owner ONLY --}}
        @if (Auth::check() && $comment->user && $comment->user->id === Auth::id())
            <div class="mt-2 comment-edit-form-wrapper" x-show="showEdit" x-transition>
                @include('themes/' . $theme_directory . '/partials/comment-edit-form', [
                    'commentOrReply' => $comment,
                ])
            </div>
        @endif

        {{-- Reply form for other users --}}
        @if (Auth::check() && $comment->user && !$isReply && $comment->user->id !== Auth::id())
            <div class="mt-2 reply-form" x-show="showReply" x-transition>
                @include('themes/' . $theme_directory . '/partials/comment-form', [
                    'article' => $article,
                    'parent_id' => $comment->id,
                ])
            </div>
        @endif
    </div>

    {{-- Replies --}}
    @if (!$isReply && $comment->replies && count($comment->replies))
        <div class="replies ps-4 mt-2">
            @foreach ($comment->replies as $reply)
                @include('themes/' . $theme_directory . '/partials/comment-card', [
                    'comment' => $reply,
                    'isReply' => true,
                    'article' => $article,
                ])
            @endforeach
        </div>
    @endif
</div>

