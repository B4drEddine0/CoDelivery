<?php

namespace Database\Seeders;

use App\Models\Command;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;

class CommandSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get client and livreur IDs
        $clientIds = User::where('role', 'client')->pluck('id')->toArray();
        $livreurIds = User::where('role', 'livreur')->pluck('id')->toArray();
        
        $serviceTypes = ['restaurant', 'pharmacy', 'market', 'other'];
        $priorities = ['low', 'medium', 'high'];
        $statuses = ['pending', 'accepted', 'in_progress', 'delivered', 'cancelled'];
        
        $restaurantNames = [
            'McDonald\'s', 'Burger King', 'KFC', 'Pizza Hut', 'Domino\'s Pizza',
            'Subway', 'Taco Bell', 'Wendy\'s', 'Starbucks', 'Dunkin\' Donuts'
        ];
        
        $pharmacyNames = [
            'Pharmacie Centrale', 'Pharmacie du Marché', 'Pharmacie de la Gare',
            'Pharmacie de la Poste', 'Pharmacie du Parc'
        ];
        
        $marketNames = [
            'Carrefour', 'Auchan', 'Lidl', 'Aldi', 'Intermarché',
            'E.Leclerc', 'Casino', 'Monoprix', 'Franprix', 'Géant Casino'
        ];
        
        $otherNames = [
            'Fnac', 'Darty', 'Boulanger', 'Decathlon', 'Leroy Merlin',
            'Ikea', 'Conforama', 'But', 'La Redoute', 'Amazon'
        ];
        
        $addresses = [
            'Casablanca' => [
                '123 Boulevard Mohammed V, Casablanca',
                '456 Rue Moulay Youssef, Casablanca',
                '789 Avenue Hassan II, Casablanca',
                '321 Rue Abou Bakr El Kadiri, Casablanca',
                '654 Boulevard Zerktouni, Casablanca',
            ],
            'Rabat' => [
                '123 Avenue Mohammed V, Rabat',
                '456 Rue des FAR, Rabat',
                '789 Avenue Allal Ben Abdellah, Rabat',
                '321 Boulevard Hassan II, Rabat',
                '654 Rue Oukaimeden, Rabat',
            ],
            'Marrakech' => [
                '123 Avenue Mohammed VI, Marrakech',
                '456 Rue Imam Malik, Marrakech',
                '789 Boulevard Allal El Fassi, Marrakech',
                '321 Avenue Guéliz, Marrakech',
                '654 Rue Ibn Sina, Marrakech',
            ],
        ];
        
        // Create 30 commands with different statuses
        for ($i = 1; $i <= 30; $i++) {
            $serviceType = $serviceTypes[array_rand($serviceTypes)];
            $priority = $priorities[array_rand($priorities)];
            $status = $statuses[array_rand($statuses)];
            $clientId = $clientIds[array_rand($clientIds)];
            
            // Determine establishment name based on service type
            switch ($serviceType) {
                case 'restaurant':
                    $establishmentName = $restaurantNames[array_rand($restaurantNames)];
                    break;
                case 'pharmacy':
                    $establishmentName = $pharmacyNames[array_rand($pharmacyNames)];
                    break;
                case 'market':
                    $establishmentName = $marketNames[array_rand($marketNames)];
                    break;
                default:
                    $establishmentName = $otherNames[array_rand($otherNames)];
                    break;
            }
            
            // Select random city and addresses
            $city = array_rand($addresses);
            $pickupAddress = $addresses[$city][array_rand($addresses[$city])];
            $deliveryAddress = $addresses[$city][array_rand($addresses[$city])];
            
            // Make sure pickup and delivery addresses are different
            while ($pickupAddress === $deliveryAddress) {
                $deliveryAddress = $addresses[$city][array_rand($addresses[$city])];
            }
            
            // Set dates based on status
            $createdAt = Carbon::now()->subDays(rand(0, 10))->subHours(rand(0, 23));
            $acceptedAt = null;
            $deliveredAt = null;
            $livreurId = null;
            
            if (in_array($status, ['accepted', 'in_progress', 'delivered'])) {
                $livreurId = $livreurIds[array_rand($livreurIds)];
                $acceptedAt = (clone $createdAt)->addHours(rand(1, 3));
            }
            
            if ($status === 'delivered') {
                $deliveredAt = (clone $acceptedAt)->addHours(rand(1, 2));
            }
            
            // Generate a random price between 30 and 150 DH
            $price = rand(30, 150);
            
            Command::create([
                'client_id' => $clientId,
                'livreur_id' => $livreurId,
                'title' => "Commande #{$i} - " . ucfirst($serviceType),
                'description' => "Description de la commande #{$i}",
                'service_type' => $serviceType,
                'establishment_name' => $establishmentName,
                'pickup_address' => $pickupAddress,
                'delivery_address' => $deliveryAddress,
                'price' => $price,
                'status' => $status,
                'priority' => $priority,
                'accepted_at' => $acceptedAt,
                'delivered_at' => $deliveredAt,
                'created_at' => $createdAt,
                'updated_at' => $status === 'delivered' ? $deliveredAt : ($status === 'accepted' ? $acceptedAt : $createdAt),
            ]);
        }
    }
}
