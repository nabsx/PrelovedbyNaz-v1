@extends('layouts.app')

@section('content')
<div class="max-w-4xl mx-auto px-4 py-8">
    <div class="mb-8">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-3xl font-bold text-gray-900">Detail User</h1>
                <p class="text-gray-600 mt-2">Informasi detail user {{ $user->name }}</p>
            </div>
            <a href="{{ route('admin.users.index') }}" 
               class="bg-gray-300 text-gray-700 px-4 py-2 rounded-lg hover:bg-gray-400 transition-colors">
                Kembali
            </a>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
        <!-- User Info -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow-md p-6">
                <div class="text-center">
                    <div class="mx-auto w-20 h-20 bg-pink-100 rounded-full flex items-center justify-center mb-4">
                        <span class="text-pink-600 font-semibold text-2xl">
                            {{ strtoupper(substr($user->name, 0, 1)) }}
                        </span>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900">{{ $user->name }}</h2>
                    <p class="text-gray-600">{{ $user->email }}</p>
                    
                    <div class="mt-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium 
                            {{ $user->role === 'admin' ? 'bg-purple-100 text-purple-800' : 'bg-blue-100 text-blue-800' }}">
                            {{ ucfirst($user->role) }}
                        </span>
                    </div>
                    
                    <div class="mt-6 text-sm text-gray-500">
                        <p>Bergabung: {{ $user->created_at->format('d M Y') }}</p>
                        <p>Terakhir update: {{ $user->updated_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- User Transactions -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow-md p-6">
                <h3 class="text-lg font-semibold text-gray-900 mb-4">Riwayat Transaksi</h3>
                
                @if($userTransactions->count() > 0)
                    <div class="space-y-4">
                        @foreach($userTransactions as $transaction)
                        <div class="border border-gray-200 rounded-lg p-4">
                            <div class="flex justify-between items-start mb-2">
                                <div>
                                    <h4 class="font-medium text-gray-900">Order #{{ $transaction->midtrans_order_id }}</h4>
                                    <p class="text-sm text-gray-500">{{ $transaction->created_at->format('d M Y H:i') }}</p>
                                </div>
                                <div class="text-right">
                                    <span class="inline-flex items-center px-2 py-1 rounded-full text-xs font-medium 
                                        {{ $transaction->status === 'paid' ? 'bg-green-100 text-green-800' : 
                                           ($transaction->status === 'pending' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                        {{ strtoupper($transaction->status) }}
                                    </span>
                                    <p class="text-lg font-bold text-pink-600 mt-1">
                                        Rp {{ number_format($transaction->total_price, 0, ',', '.') }}
                                    </p>
                                </div>
                            </div>
                            
                            <div class="text-sm text-gray-600">
                                <strong>Items:</strong>
                                @foreach($transaction->items as $item)
                                <div>{{ $item->product->name }} (x{{ $item->quantity }})</div>
                                @endforeach
                            </div>
                        </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-8">
                        <i class="fas fa-receipt text-gray-300 text-4xl mb-3"></i>
                        <p class="text-gray-500">Belum ada transaksi</p>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection