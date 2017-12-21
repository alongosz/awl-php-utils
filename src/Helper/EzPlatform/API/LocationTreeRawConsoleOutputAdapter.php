<?php


namespace Awl\Helper\EzPlatform\API;

use eZ\Publish\API\Repository\Values\Content\Location;
use eZ\Publish\API\Repository\LocationService;

class LocationTreeRawConsoleOutputAdapter
{
    /**
     * @var \eZ\Publish\API\Repository\LocationService
     */
    private $locationService;

    public function __construct(LocationService $locationService)
    {
        $this->locationService = $locationService;
    }

    /**
     * @param \eZ\Publish\API\Repository\Values\Content\Location $location
     * @param int $depth
     */
    public function displayChildren(Location $location, $depth = 0)
    {
        $locationList = $this->locationService->loadLocationChildren($location);

        foreach ($locationList->locations as $location) {
            echo sprintf(
                '%s[%d] #%d %s (Main Location Id = %d, Content Type Id = %d)',
                str_repeat("\t", $depth),
                $location->id,
                $location->contentId,
                $location->contentInfo->name,
                $location->contentInfo->mainLocationId,
                $location->contentInfo->contentTypeId
            ), "\r\n";
            $this->displayChildren($location, ++$depth);
        }
    }
}
