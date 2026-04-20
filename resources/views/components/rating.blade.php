@props(['course', 'userReview' => null])

<div class="bg-white rounded-xl shadow-sm p-6">
    <h3 class="text-lg font-semibold text-gray-900 mb-4">Évaluations</h3>
    
    <div class="flex items-center space-x-8 mb-6">
        <div class="text-center">
            <div class="text-5xl font-bold text-gray-900 mb-2">{{ number_format($course->average_rating, 1) }}</div>
            <div class="flex text-yellow-400 justify-center mb-1">
                @for($i = 1; $i <= 5; $i++)
                    @if($i <= round($course->average_rating))
                        ★
                    @else
                        ☆
                    @endif
                @endfor
            </div>
            <div class="text-sm text-gray-500">{{ $course->reviews_count }} avis</div>
        </div>
        
        <div class="flex-1">
            @for($i = 5; $i >= 1; $i--)
                @php
                    $count = $course->reviews()->where('rating', $i)->count();
                    $percentage = $course->reviews_count > 0 ? ($count / $course->reviews_count) * 100 : 0;
                @endphp
                <div class="flex items-center mb-1">
                    <span class="text-sm text-gray-600 w-12">{{ $i }} étoiles</span>
                    <div class="flex-1 mx-3 h-2 bg-gray-200 rounded-full overflow-hidden">
                        <div class="h-full bg-yellow-400 rounded-full" style="width: {{ $percentage }}%"></div>
                    </div>
                    <span class="text-sm text-gray-500 w-10">{{ $count }}</span>
                </div>
            @endfor
        </div>
    </div>
    
    @auth
        @if($course->students->contains(Auth::id()))
            <form action="{{ route('reviews.store', $course) }}" method="POST" class="border-t pt-6">
                @csrf
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Votre note</label>
                    <div class="flex space-x-2" x-data="{ rating: {{ $userReview->rating ?? 0 }} }">
                        @for($i = 1; $i <= 5; $i++)
                            <button type="button" @click="rating = {{ $i }}" class="text-3xl focus:outline-none">
                                <span x-show="rating >= {{ $i }}" class="text-yellow-400">★</span>
                                <span x-show="rating < {{ $i }}" class="text-gray-300">☆</span>
                            </button>
                        @endfor
                        <input type="hidden" name="rating" x-model="rating">
                    </div>
                </div>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Votre commentaire (optionnel)</label>
                    <textarea name="comment" 
                              rows="3"
                              placeholder="Partagez votre expérience..."
                              class="w-full rounded-lg border-gray-300 focus:border-indigo-500 focus:ring-indigo-500">{{ old('comment', $userReview->comment ?? '') }}</textarea>
                </div>
                
                <button type="submit" 
                        class="px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                    {{ $userReview ? 'Mettre à jour' : 'Publier' }}
                </button>
            </form>
        @endif
    @endauth
    
    @if($course->reviews->count() > 0)
        <div class="border-t mt-6 pt-6 space-y-4">
            @foreach($course->reviews()->latest()->take(5)->get() as $review)
                <div class="border-b border-gray-100 last:border-0 pb-4 last:pb-0">
                    <div class="flex items-center mb-2">
                        <img src="{{ $review->user->avatar }}" class="w-8 h-8 rounded-full mr-3">
                        <div>
                            <div class="font-medium text-gray-900">{{ $review->user->name }}</div>
                            <div class="flex text-yellow-400 text-sm">
                                {!! $review->rating_stars !!}
                            </div>
                        </div>
                        <span class="ml-auto text-sm text-gray-500">{{ $review->created_at->diffForHumans() }}</span>
                    </div>
                    @if($review->comment)
                        <p class="text-gray-600 text-sm ml-11">{{ $review->comment }}</p>
                    @endif
                </div>
            @endforeach
        </div>
    @endif
</div>