@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Riwayat Transaksi</h1>
        <p class="text-gray-600 mt-2">Lihat riwayat pembelian Anda</p>
    </div>

    @if($transactions->count() > 0)
        <div class="space-y-6">
            @foreach($transactions as $transaction)
            <div class="bg-white rounded-lg shadow-md overflow-hidden">
                <div class="p-6">
                    <div class="flex justify-between items-start mb-4">
                        <div>
                            <h3 class="text-lg font-semibold text-gray-900">
                                Order #{{ $transaction->midtrans_order_id }}
                            </h3>
                            <p class="text-gray-600 text-sm">
                                {{ $transaction->created_at->format('d M Y H:i') }}
                            </p>
                        </div>
                        <div class="text-right">
                            <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                                @if($transaction->status === 'paid') bg-green-100 text-green-800
                                @elseif($transaction->status === 'pending') bg-yellow-100 text-yellow-800
                                @elseif($transaction->status === 'expired') bg-red-100 text-red-800
                                @else bg-gray-100 text-gray-800 @endif">
                                {{ strtoupper($transaction->status) }}
                            </span>
                            <p class="text-xl font-bold text-pink-600 mt-2">
                                Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                            </p>
                        </div>
                    </div>

                    <div class="border-t border-gray-200 pt-4">
                        <h4 class="font-semibold text-gray-900 mb-3">Items:</h4>
                        <div class="space-y-2">
                            @foreach($transaction->items as $item)
                            <div class="flex justify-between items-center">
                                <div class="flex items-center space-x-3">
                                    <img src="https://via.placeholder.com/50x50?text=Preloved" 
                                         alt="{{ $item->product->name }}"
                                         class="w-10 h-10 object-cover rounded">
                                    <div>
                                        <p class="text-gray-900 font-medium">{{ $item->product->name }}</p>
                                        <p class="text-gray-600 text-sm">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                <span class="text-gray-900">Rp {{ number_format($item->price * $item->quantity, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    @if($transaction->status === 'pending' && !$transaction->isExpired())
                    <div class="mt-4 pt-4 border-t border-gray-200">
                        <a href="#" 
                           class="inline-flex items-center px-4 py-2 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors">
                            <i class="fas fa-credit-card mr-2"></i>
                            Lanjutkan Pembayaran
                        </a>
                    </div>
                    @endif
                </div>
            </div>
            @endforeach
        </div>

        <div class="mt-8">
            {{ $transactions->links() }}
        </div>
    @else
        <div class="text-center py-12">
            <div class="mx-auto w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                <i class="fas fa-receipt text-gray-400 text-3xl"></i>
            </div>
            <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Transaksi</h3>
            <p class="text-gray-600 mb-6">Anda belum melakukan transaksi apapun</p>
            <a href="{{ route('products.index') }}" 
               class="inline-flex items-center px-6 py-3 bg-pink-500 text-white rounded-lg hover:bg-pink-600 transition-colors">
                <i class="fas fa-shopping-bag mr-2"></i>
                Mulai Belanja
            </a>
        </div>
    @endif
</div>
@endsection