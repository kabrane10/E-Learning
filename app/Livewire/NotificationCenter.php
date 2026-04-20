<?php

namespace App\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class NotificationCenter extends Component
{
    public $notifications = [];
    public $unreadCount = 0;
    
    public function mount()
    {
        $this->loadNotifications();
    }
    
    public function loadNotifications()
    {
        $user = Auth::user();
        if ($user) {
            $this->notifications = $user->notifications()->latest()->take(20)->get();
            $this->unreadCount = $user->unreadNotifications()->count();
        }
    }
    
    public function markAsRead($notificationId)
    {
        $notification = Auth::user()->notifications()->find($notificationId);
        if ($notification) {
            $notification->markAsRead();
            $this->loadNotifications();
        }
    }
    
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        $this->loadNotifications();
    }
    
    public function delete($notificationId)
    {
        Auth::user()->notifications()->where('id', $notificationId)->delete();
        $this->loadNotifications();
    }
    
    public function render()
    {
        return view('livewire.notification-center');
    }
}