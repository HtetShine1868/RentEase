@extends('dashboard')

@section('title', 'My Reviews')

@section('content')
<div class="max-w-4xl mx-auto">
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <!-- Header -->
        <div class="px-6 py-4 bg-gradient-to-r from-indigo-600 to-purple-600">
            <h2 class="text-xl font-bold text-white">My Reviews</h2>
            <p class="text-indigo-100 text-sm mt-1">All your ratings and feedback</p>
        </div>

        <!-- Reviews List -->
        <div class="p-6">
            @if($ratings->count() > 0)
                <div class="space-y-6">
                    @foreach($ratings as $rating)
                        <div class="border border-gray-200 rounded-lg p-4 hover:shadow-md transition-shadow">
                            <div class="flex items-start justify-between">
                                <div>
                                    <h3 class="font-semibold text-gray-900">{{ $rating->serviceProvider->business_name }}</h3>
                                    <p class="text-sm text-gray-600 mt-1">
                                        Order #{{ $rating->order->order_reference }} • 
                                        {{ $rating->created_at->format('M d, Y') }}
                                    </p>
                                </div>
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= $rating->overall_rating)
                                            <i class="fas fa-star"></i>
                                        @else
                                            <i class="far fa-star"></i>
                                        @endif
                                    @endfor
                                </div>
                            </div>

                            <!-- Rating Breakdown -->
                            <div class="mt-3 grid grid-cols-3 gap-4 max-w-md">
                                <div class="text-sm">
                                    <span class="text-gray-500">Quality:</span>
                                    <span class="ml-1 font-medium">{{ $rating->quality_rating }}/5</span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-500">Delivery:</span>
                                    <span class="ml-1 font-medium">{{ $rating->delivery_rating }}/5</span>
                                </div>
                                <div class="text-sm">
                                    <span class="text-gray-500">Value:</span>
                                    <span class="ml-1 font-medium">{{ $rating->value_rating }}/5</span>
                                </div>
                            </div>

                            @if($rating->comment)
                                <p class="mt-3 text-sm text-gray-700">{{ $rating->comment }}</p>
                            @endif

                            @if($rating->images)
                                <div class="mt-3 flex gap-2">
                                    @foreach($rating->images as $image)
                                        <img src="{{ $image }}" class="w-16 h-16 object-cover rounded-lg border border-gray-200">
                                    @endforeach
                                </div>
                            @endif

                            <div class="mt-4 flex justify-end space-x-2">
                                <a href="{{ route('food.rate.edit', $rating->order) }}" 
                                   class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                                    <i class="fas fa-edit mr-1"></i>Edit
                                </a>
                                <form action="{{ route('food.rate.destroy', $rating->order) }}" method="POST" class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" onclick="return confirm('Are you sure?')"
                                            class="text-red-600 hover:text-red-900 text-sm font-medium">
                                        <i class="fas fa-trash mr-1"></i>Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                @if($ratings->hasPages())
                    <div class="mt-6">
                        {{ $ratings->links() }}
                    </div>
                @endif
            @else
                <div class="text-center py-12">
                    <i class="fas fa-star text-gray-300 text-5xl mb-4"></i>
                    <h3 class="text-lg font-medium text-gray-900">No reviews yet</h3>
                    <p class="mt-2 text-gray-500">Your reviews will appear here after you rate your orders</p>
                    <a href="{{ route('food.orders') }}" 
                       class="mt-4 inline-flex items-center px-4 py-2 bg-indigo-600 text-white rounded-lg hover:bg-indigo-700">
                        View Orders
                    </a>
                </div>
            @endif
        </div>
    </div>
</div>
@endsection