<?php

declare(strict_types=1);

namespace App\Presets;

class SearchPreset
{
    /**
     * @param  string[]  $envTypes
     */
    public function __construct(
        public readonly string $id,
        public readonly string $name,
        public readonly string $description,
        public readonly string $emoji,
        public readonly ?string $baseName = null,
        public readonly ?string $interval = null,
        public readonly ?float $minMa = null,
        public readonly ?float $maxMa = null,
        public readonly array $envTypes = [],
        public readonly ?string $countryCodes = null,
    ) {}

    /**
     * All curated preset searches, ordered for discovery.
     *
     * @return static[]
     */
    public static function all(): array
    {
        return [
            new static(
                id: 'age-of-dinosaurs',
                name: 'Age of Dinosaurs',
                description: 'Non-avian dinosaurs from the Triassic through the end-Cretaceous extinction.',
                emoji: '🦕',
                baseName: 'Dinosauria',
                minMa: 66.0,
                maxMa: 252.0,
            ),
            new static(
                id: 't-rex-country',
                name: 'T. rex Country',
                description: 'Tyrannosaurid fossils from North America during the Late Cretaceous.',
                emoji: '🦖',
                baseName: 'Tyrannosauridae',
                interval: 'Cretaceous',
                countryCodes: 'US',
            ),
            new static(
                id: 'ice-age-giants',
                name: 'Ice Age Giants',
                description: 'Pleistocene megafauna — woolly mammoths, mastodons, and giant sloths.',
                emoji: '🧊',
                baseName: 'Mammalia',
                minMa: 0.0,
                maxMa: 2.6,
            ),
            new static(
                id: 'rise-of-mammals',
                name: 'Rise of Mammals',
                description: 'Mammal fossils from the Paleogene — the era that followed the dinosaur extinction.',
                emoji: '🦣',
                baseName: 'Mammalia',
                minMa: 23.0,
                maxMa: 66.0,
            ),
            new static(
                id: 'trilobite-world',
                name: 'Trilobite World',
                description: 'Trilobites — armoured arthropods that dominated the seas for 270 million years.',
                emoji: '🪲',
                baseName: 'Trilobita',
            ),
            new static(
                id: 'ammonites',
                name: 'Ammonites',
                description: 'Coiled cephalopods — some of the most iconic marine fossils, spanning 350 million years.',
                emoji: '🐚',
                baseName: 'Ammonoidea',
            ),
            new static(
                id: 'cambrian-seas',
                name: 'Cambrian Seas',
                description: 'Marine life from the Cambrian Explosion — the dawn of complex animal life.',
                emoji: '🌊',
                interval: 'Cambrian',
                envTypes: ['marine'],
            ),
            new static(
                id: 'great-dying',
                name: 'The Great Dying',
                description: "Fossils from around the Permian–Triassic boundary — Earth's worst mass extinction.",
                emoji: '💀',
                minMa: 245.0,
                maxMa: 260.0,
            ),
        ];
    }

    /**
     * Find a preset by its ID, or return null if not found.
     */
    public static function find(string $id): ?static
    {
        foreach (static::all() as $preset) {
            if ($preset->id === $id) {
                return $preset;
            }
        }

        return null;
    }
}
