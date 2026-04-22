<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Api\Contracts\FossilOccurrenceServiceInterface;
use App\Api\Exceptions\ApiException;
use App\Api\Queries\OccurrenceQuery;
use App\DTOs\OccurrenceCollection;
use Illuminate\Contracts\View\View;

class TaxonController extends Controller
{
    /**
     * A curated selection of notable fossil taxa for the taxa index page.
     *
     * @var array<int, array{name: string, description: string, era: string, rank: string}>
     */
    private const FEATURED_TAXA = [
        [
            'name' => 'Dinosauria',
            'description' => 'The dominant terrestrial vertebrates of the Mesozoic Era. From tiny feathered hunters to the largest animals to ever walk the Earth.',
            'era' => 'Triassic – Cretaceous',
            'rank' => 'Clade',
        ],
        [
            'name' => 'Trilobita',
            'description' => 'Ancient marine arthropods that flourished for over 270 million years before vanishing at the end-Permian mass extinction.',
            'era' => 'Cambrian – Permian',
            'rank' => 'Class',
        ],
        [
            'name' => 'Mammalia',
            'description' => 'Warm-blooded vertebrates defined by hair, live birth, and milk production. Includes everything from bats to whales to humans.',
            'era' => 'Triassic – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Ammonoidea',
            'description' => 'Extinct cephalopod mollusks with intricately chambered shells. Prized by collectors and used as index fossils to date rock strata.',
            'era' => 'Devonian – Cretaceous',
            'rank' => 'Subclass',
        ],
        [
            'name' => 'Bivalvia',
            'description' => 'Clams, oysters, and mussels. Bivalves have been filtering sediment from the seafloor for over 500 million years.',
            'era' => 'Cambrian – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Gastropoda',
            'description' => 'The most species-rich class of mollusks. Snails and slugs have colonised land, sea, and freshwater environments across the globe.',
            'era' => 'Cambrian – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Echinodermata',
            'description' => 'Sea urchins, starfish, and crinoids. Echinoderms are exclusively marine and share a distinctive five-fold body symmetry.',
            'era' => 'Cambrian – Present',
            'rank' => 'Phylum',
        ],
        [
            'name' => 'Aves',
            'description' => 'Living dinosaurs. Birds are the only surviving lineage of theropod dinosaurs, with a fossil record stretching back to the Jurassic.',
            'era' => 'Jurassic – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Foraminifera',
            'description' => 'Single-celled protists that build intricate calcium carbonate shells. An essential biostratigraphic tool for dating marine sediments.',
            'era' => 'Cambrian – Present',
            'rank' => 'Order',
        ],
        [
            'name' => 'Cephalopoda',
            'description' => 'The most intelligent invertebrates — nautiloids, belemnites, ammonites, and their living descendants: squid and octopuses.',
            'era' => 'Cambrian – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Reptilia',
            'description' => 'A sprawling vertebrate grade including lizards, snakes, turtles, crocodilians, and extinct marine reptiles and pterosaurs.',
            'era' => 'Carboniferous – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Plantae',
            'description' => 'Land plants have reshaped Earth\'s atmosphere and built its terrestrial habitats. Their fossil record begins with spores in the Ordovician.',
            'era' => 'Ordovician – Present',
            'rank' => 'Kingdom',
        ],
        [
            'name' => 'Brachiopoda',
            'description' => 'Lamp shells — superficially clam-like but phylogenetically distinct. Once dominant on Paleozoic seafloors, now mostly restricted to cold deep water.',
            'era' => 'Cambrian – Present',
            'rank' => 'Phylum',
        ],
        [
            'name' => 'Anthozoa',
            'description' => 'Corals and sea anemones. Reef-building corals have constructed the largest biological structures on Earth across hundreds of millions of years.',
            'era' => 'Ordovician – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Chondrichthyes',
            'description' => 'Sharks, rays, and chimaeras. Cartilaginous fish with a fossil record stretching back 450 million years, surviving every mass extinction.',
            'era' => 'Ordovician – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Actinopterygii',
            'description' => 'Ray-finned fish — the most species-rich vertebrate group. From the first bony fish of the Devonian to salmon, tuna, and eels today.',
            'era' => 'Silurian – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Pterosauria',
            'description' => 'The first vertebrates to achieve powered flight. Pterosaurs ranged from sparrow-sized to the largest flying animals ever known.',
            'era' => 'Triassic – Cretaceous',
            'rank' => 'Order',
        ],
        [
            'name' => 'Ichthyosauria',
            'description' => 'Streamlined marine reptiles that convergently evolved a dolphin-like body plan. They gave birth to live young in the open ocean.',
            'era' => 'Triassic – Cretaceous',
            'rank' => 'Order',
        ],
        [
            'name' => 'Graptolithina',
            'description' => 'Colonial organisms that floated in ancient seas. Their distinctive saw-blade fossils are among the most reliable index fossils for Paleozoic strata.',
            'era' => 'Cambrian – Carboniferous',
            'rank' => 'Class',
        ],
        [
            'name' => 'Crinoidea',
            'description' => 'Sea lilies and feather stars. Crinoids carpeted shallow Paleozoic seafloors so densely that entire limestone formations are composed of their remains.',
            'era' => 'Ordovician – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Bryozoa',
            'description' => 'Tiny colonial filter feeders that build intricate calcified frameworks. A quiet but abundant presence on marine substrates since the Ordovician.',
            'era' => 'Ordovician – Present',
            'rank' => 'Phylum',
        ],
        [
            'name' => 'Ostracoda',
            'description' => 'Microscopic bivalved crustaceans with one of the richest fossil records of any group. Indispensable for correlating rock layers across continents.',
            'era' => 'Cambrian – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Insecta',
            'description' => 'The most species-rich class of animals. Insects conquered the land and air in the Devonian and are spectacularly preserved in amber.',
            'era' => 'Devonian – Present',
            'rank' => 'Class',
        ],
        [
            'name' => 'Porifera',
            'description' => 'Sponges — the simplest of all animals. Archaeocyathid sponges built the earliest reefs in the Cambrian and filter the ocean to this day.',
            'era' => 'Cryogenian – Present',
            'rank' => 'Phylum',
        ],
    ];

    /**
     * Display the curated taxa index page.
     */
    public function index(): View
    {
        return view('taxa.index', [
            'taxa' => self::FEATURED_TAXA,
        ])->with('title', 'Taxa');
    }

    /**
     * Display the taxon page for the given taxon name.
     */
    public function show(string $name): View
    {
        /** @var FossilOccurrenceServiceInterface $service */
        $service = app(FossilOccurrenceServiceInterface::class);

        try {
            $query = new OccurrenceQuery(
                baseName: $name,
                show: 'coords,class,loc,time',
                limit: 1000,
            );

            $occurrences = $service->getOccurrences($query);
        } catch (ApiException) {
            $occurrences = new OccurrenceCollection(items: [], total: 0, offset: 0);
        }

        $totalCount = $occurrences->total ?: count($occurrences->items);
        $fetchedCount = count($occurrences->items);

        // Compute classification breakdown from the occurrence set.
        $byPhylum = [];
        $byClass = [];
        $byEnvironment = [];

        foreach ($occurrences->items as $occ) {
            if ($occ->phylum !== null) {
                $byPhylum[$occ->phylum] = ($byPhylum[$occ->phylum] ?? 0) + 1;
            }
            if ($occ->class !== null) {
                $byClass[$occ->class] = ($byClass[$occ->class] ?? 0) + 1;
            }
            if ($occ->environment !== null) {
                $byEnvironment[$occ->environment] = ($byEnvironment[$occ->environment] ?? 0) + 1;
            }
        }

        arsort($byPhylum);
        arsort($byClass);
        arsort($byEnvironment);

        return view('taxa.show', compact(
            'name',
            'occurrences',
            'totalCount',
            'fetchedCount',
            'byPhylum',
            'byClass',
            'byEnvironment',
        ))->with('title', $name);
    }
}
