<?php

namespace App\Filament\Widgets;

use App\Models\Feeding;
use App\Models\HealthFlag;
use App\Models\PlaySession;
use App\Models\VetVisit;
use App\Models\Walk;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class LogisticsOverview extends StatsOverviewWidget
{
    protected static ?int $sort = -1;

    protected ?string $heading = 'Today at a glance';

    protected function getStats(): array
    {
        $dogIds = auth()->user()->dogs()->pluck('id');
        $today = now()->startOfDay();

        $feedingsToday = Feeding::whereIn('dog_id', $dogIds)->where('fed_at', '>=', $today)->count();
        $lastFed = Feeding::whereIn('dog_id', $dogIds)->latest('fed_at')->first();

        $walksToday = Walk::whereIn('dog_id', $dogIds)->where('started_at', '>=', $today);
        $walkCount = (clone $walksToday)->count();
        $walkMinutes = (int) (clone $walksToday)->sum('duration_min');
        $lastWalk = Walk::whereIn('dog_id', $dogIds)->latest('started_at')->first();

        $playWeek = PlaySession::whereIn('dog_id', $dogIds)->where('played_at', '>=', now()->subDays(7));
        $lastPlay = PlaySession::whereIn('dog_id', $dogIds)->latest('played_at')->first();

        $openFlags = HealthFlag::whereIn('dog_id', $dogIds)->where('status', 'open')->count();
        $highFlags = HealthFlag::whereIn('dog_id', $dogIds)->where('status', 'open')->where('severity', 'high')->count();

        $nextVet = VetVisit::whereIn('dog_id', $dogIds)->whereDate('next_due_on', '>=', $today)->orderBy('next_due_on')->first();

        return [
            Stat::make('Feedings today', $feedingsToday)
                ->description($lastFed ? 'Last fed '.$lastFed->fed_at->diffForHumans() : 'No feedings logged')
                ->icon('heroicon-o-cake')
                ->color('success'),
            Stat::make('Walks today', $walkCount)
                ->description($walkMinutes.' min'.($lastWalk ? ' · last '.$lastWalk->started_at->diffForHumans() : ''))
                ->icon('heroicon-o-map-pin')
                ->color('info'),
            Stat::make('Play (7 days)', (clone $playWeek)->count())
                ->description($lastPlay ? 'Last played '.$lastPlay->played_at->diffForHumans() : 'No play logged')
                ->icon('heroicon-o-trophy')
                ->color('warning'),
            Stat::make('Open health flags', $openFlags)
                ->description($highFlags ? $highFlags.' high severity' : 'None high severity')
                ->icon('heroicon-o-flag')
                ->color($highFlags ? 'danger' : ($openFlags ? 'warning' : 'success')),
            Stat::make('Next vet due', $nextVet ? $nextVet->next_due_on->format('M j, Y') : 'Not scheduled')
                ->description($nextVet ? $nextVet->next_due_on->diffForHumans() : 'No upcoming vet visit')
                ->icon('heroicon-o-heart')
                ->color('primary'),
        ];
    }
}
