<?php

namespace Database\Seeders;

use App\Models\Faq;
use App\Models\Post;
use App\Models\Project;
use App\Models\Service;
use App\Models\Team;
use Illuminate\Database\Seeder;

/**
 * Sample content so a fresh demo site (e.g. Render free plan) isn't empty.
 * php artisan db:seed --class=Database\\Seeders\\DemoSeeder
 * Uses only images that ship with the theme. Safe to run once on an empty site.
 */
class DemoSeeder extends Seeder
{
    public function run(): void
    {
        if (Service::exists()) {
            return; // already has real content
        }

        $services = [
            ['Lawn Mowing', 'Neat, even cuts with edging and clean-up after every visit.', 800, 40, 1, 3],
            ['Hedge Pruning', 'Shape and health for hedges, shrubs and small trees.', 1200, null, 2, 4],
            ['Garden Design', 'A planting and layout plan made for your space and budget.', 2500, null, 3, 5],
            ['Garden Maintenance', 'Weeding, feeding and seasonal care on a regular schedule.', 1500, 30, 4, 6],
            ['Irrigation & Drainage', 'Watering systems and drainage fixes for waterlogged lawns.', 3000, null, 5, 7],
            ['Urban Gardening', 'Rooftop and balcony gardens with pots, beds and vertical planters.', 2000, null, 6, 8],
        ];

        foreach ($services as [$title, $desc, $price, $per100, $img, $icon]) {
            Service::create([
                'title' => $title,
                'description' => $desc,
                'long_description' => $desc."\n\nAll tools and materials are included. Tell us about your space when you book and we will confirm the final price after a quick look.",
                'price' => $price,
                'price_per_100_sqft' => $per100,
                'image_url' => "site/img/service-{$img}.jpg",
                'icon_url' => "site/img/icon/icon-{$icon}.png",
            ]);
        }

        foreach ([['Rahim Uddin', 'Senior Gardener', 1], ['Karim Mia', 'Landscaper', 2], ['Nusrat Jahan', 'Garden Designer', 3]] as [$name, $position, $img]) {
            Team::create(['title' => $name, 'position' => $position, 'image_url' => "site/img/team-{$img}.jpg"]);
        }

        foreach ([['Family Lawn Makeover', 'complete', 1], ['Hedge Shaping', 'ongoing', 2], ['Backyard Drainage', 'complete', 3], ['Rooftop Garden', 'ongoing', 6]] as [$title, $cat, $img]) {
            Project::create(['title' => $title, 'category' => $cat, 'image_url' => "site/img/service-{$img}.jpg"]);
        }

        $faqs = [
            ['Which areas do you cover?', 'Narail, Jashore and nearby areas. Ask us on WhatsApp if you are further away.'],
            ['How do I pay?', 'Pay online with bKash, Nagad or card, or pay your gardener in cash on the day.'],
            ['Can I book a repeating service?', 'Yes — choose weekly, every 2 weeks or monthly when you book. You can stop it any time from your dashboard.'],
            ['What if I need to change the date?', 'Open your dashboard and press "Reschedule" on a pending booking.'],
        ];
        foreach ($faqs as $i => [$q, $a]) {
            Faq::create(['question' => $q, 'answer' => $a, 'sort_order' => $i + 1]);
        }

        Post::create([
            'title' => '5 Tips for a Green Lawn in Winter',
            'slug' => '5-tips-for-a-green-lawn-in-winter',
            'excerpt' => 'Keep your grass healthy through the dry months with these simple habits.',
            'body' => "1. Water early in the morning, not at night.\n\n2. Mow a little higher than usual so roots stay shaded.\n\n3. Rake fallen leaves before they smother the grass.\n\n4. Feed once with a slow-release fertiliser.\n\n5. Keep foot traffic off frosty or very wet grass.",
            'image_url' => 'site/img/about.jpg',
            'isPublished' => true,
            'published_at' => now(),
        ]);
    }
}
