<?php
/**
 * This file is part of the eZ Publish Kernel package.
 *
 * @copyright Copyright (C) eZ Systems AS. All rights reserved.
 * @license For full copyright and license information view LICENSE file distributed with this source code.
 * @version //autogentag//
 */

namespace eZ\Bundle\EzPublishCoreBundle\Converter;

use eZ\Publish\API\Repository\Exceptions\NotFoundException;
use eZ\Publish\API\Repository\Exceptions\UnauthorizedException;
use eZ\Publish\API\Repository\LocationService;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Request\ParamConverter\ParamConverterInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class LocationParamConverter implements ParamConverterInterface
{
    const LOCATION_ID = 'locationId';

    const LOCATION_CLASS = 'eZ\Publish\API\Repository\Values\Content\Location';

    /** @var  \eZ\Publish\API\Repository\LocationService */
    private $locationService;

    public function __construct( LocationService $locationService )
    {
        $this->locationService = $locationService;
    }

    public function apply( Request $request, ParamConverter $configuration )
    {
        try
        {
            if ( !$request->attributes->has( self::LOCATION_ID ) )
            {
                return false;
            }

            $value = $request->attributes->get( self::LOCATION_ID );
            if ( !$value && $configuration->isOptional() )
            {
                return false;
            }

            $request->attributes->set( $configuration->getName(), $this->locationService->loadLocation( $value ) );
            return true;
        }
        catch ( NotFoundException $e )
        {
            throw new NotFoundHttpException( 'Requested values not found', $e );
        }
        catch ( UnauthorizedException $e )
        {
            throw new AccessDeniedHttpException( 'Access to values denied', $e );
        }
    }

    public function supports( ParamConverter $configuration )
    {
        if ( $configuration->getClass() == self::LOCATION_CLASS )
        {
            return true;
        }

        return false;
    }
}
