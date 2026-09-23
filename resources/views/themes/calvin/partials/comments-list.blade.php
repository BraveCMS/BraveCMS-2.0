@foreach ($comments as $comment)
    @if (is_null($comment->parent_id))
        <li id="comment-{{ $comment->id }}" class="depth-1 comment">
            <div class="comment__avatar">
                <img class="avatar" src="{{ asset('images/avatars/' . ($comment->user->avatar ?? 'default.png')) }}"
                    alt="{{ $comment->user->first_name ?? 'Deleted user' }} {{ $comment->user->last_name ?? '' }}"
                    width="50" height="50">
            </div>

            <div class="comment__content">
                <div class="comment__info">
                    <div class="comment__author">
                        @if ($comment->user)
                            {{ $comment->user->first_name }} {{ $comment->user->last_name }}
                        @else
                            Deleted user
                        @endif
                    </div>

                    <div class="comment__meta">
                        <div class="comment__time">{{ date('jS M Y', strtotime($comment->created_at)) }}</div>

                        @auth
                            @if ($comment->user)
                                <div class="comment__reply">
                                    @if ($comment->user->id !== Auth::user()->id)
                                        <a href="#0" class="comment-reply-link" data-comment-id="{{ $comment->id }}">
                                            <i class="fa fa-comment"></i> Reply
                                        </a>
                                    @endif

                                    @if ($comment->user->id === Auth::user()->id)
                                        <a href="#0" class="comment-edit-link" data-comment-id="{{ $comment->id }}">
                                            <i class="fa fa-edit"></i> Edit
                                        </a>

                                        @include(
                                            'themes/' . $theme_directory . '/partials/comment-delete-form',
                                            [
                                                'commentOrReply' => $comment,
                                            ]
                                        )
                                    @endif
                                </div>
                            @endif
                        @endauth
                    </div>
                </div>

                <div class="comment__text">
                    <p>{{ $comment->body }}</p>
                </div>
            </div>

            @auth
                {{-- Reply form container --}}
                <div class="comment-replies"></div>

                {{-- Add comment/reply form --}}
                @include('themes/' . $theme_directory . '/partials/comment-form')

                {{-- Edit form --}}
                @if ($comment->user && $comment->user->id === Auth::user()->id)
                    @include('themes/' . $theme_directory . '/partials/comment-edit-form', [
                        'commentOrReply' => $comment,
                    ])
                @endif
            @endauth

            {{-- Replies --}}
            @if ($comment->replies->count())
                <ul class="children comment-replies">
                    @foreach ($comment->replies as $reply)
                        <li id="comment-{{ $reply->id }}" class="depth-2 comment">
                            <div class="comment__avatar">
                                <img class="avatar"
                                    src="{{ asset('images/avatars/' . ($reply->user->avatar ?? 'default.png')) }}"
                                    alt="{{ $reply->user->first_name ?? 'Deleted user' }} {{ $reply->user->last_name ?? '' }}"
                                    width="50" height="50">
                            </div>

                            <div class="comment__content">
                                <div class="comment__info">
                                    <div class="comment__author">
                                        @if ($reply->user)
                                            {{ $reply->user->first_name }} {{ $reply->user->last_name }}
                                        @else
                                            Deleted user
                                        @endif
                                    </div>

                                    <div class="comment__meta">
                                        <div class="comment__time">{{ date('jS M Y', strtotime($reply->created_at)) }}
                                        </div>

                                        @auth
                                            @if ($reply->user)
                                                <div class="comment__reply">
                                                    @if ($reply->user->id === Auth::user()->id)
                                                        <a href="#0" class="comment-edit-link"
                                                            data-comment-id="{{ $reply->id }}">
                                                            <i class="fa fa-edit"></i> Edit
                                                        </a>

                                                        @include(
                                                            'themes/' .
                                                                $theme_directory .
                                                                '/partials/comment-delete-form',
                                                            [
                                                                'commentOrReply' => $reply,
                                                            ]
                                                        )
                                                    @endif
                                                </div>
                                            @endif
                                        @endauth
                                    </div>
                                </div>

                                <div class="comment__text">
                                    <p>{{ $reply->body }}</p>
                                </div>
                            </div>

                            @auth
                                {{-- Edit form for reply --}}
                                @if ($reply->user && $reply->user->id === Auth::user()->id)
                                    @include('themes/' . $theme_directory . '/partials/comment-edit-form', [
                                        'commentOrReply' => $reply,
                                    ])
                                @endif
                            @endauth
                        </li>
                    @endforeach
                </ul>
            @endif
        </li>
    @endif
@endforeach
