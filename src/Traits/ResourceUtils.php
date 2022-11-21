<?php

namespace Jdlx\Traits;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Http\Resources\Json\ResourceCollection;
use Illuminate\Http\Resources\MissingValue;
use Illuminate\Support\Collection;
use \Illuminate\Support\Str;

/**
 * @mixin JsonResource
 */
trait ResourceUtils
{

    protected function getCacheKey($request)
    {
        return get_class($this->resource) . "_" . $this->resource->id . "_" . $this->getWith($request)->join(",");
    }

    public function getCacheTags(&$tags, $data)
    {
        // ok, but what if it was cached response.
        $tags = [get_class($this->resource) . $this->resource->id];
        foreach ($data as $relation => $value) {
            if ($relation === "__tags") {
                array_push($tags, ...$value);
            } else if (is_array($value)) {
                $this->getCacheTags($tags, $data);
            }
        }

        return $tags;
    }

    protected function fromCache($request)
    {
        $key = $this->getCacheKey($request);
        $cached = \Cache::get($key);

        return \Cache::get($key);
    }

    protected function toCache(&$data, $request)
    {
        $key = $this->getCacheKey($request);
        $data["__tags"] = $this->getCacheTags($tags, $data);
        dump($data["__tags"]);
        // How am I getting tags!
        \Cache::put($key, $data);
    }

    protected function getWith($request)
    {
        return collect($this->resource->__requestedWith
            ?? array_filter(explode(",", $request->get("with", ""))));
    }

    protected function whenRequested($request, $relationship)
    {
        $list = $this->getWith($request);

        // dump(class_basename($this->resource) . " requested {$relationship} in: " . $list->join(","));
        if (!$list->contains($relationship)) {
            return value(new MissingValue);
        }

        // Forward all chidren of this relationship
        $children = $list
            ->filter(fn($i) => Str::startsWith($i, "{$relationship}."))
            ->map(fn($i) => Str::replace("{$relationship}.", "", $i));

        if ($this->resource->{$relationship} instanceof Collection) {
            //    dump("Set children in collection: " . $children->join(","));
            $this->resource->{$relationship}->each(fn($r) => $r->__requestedWith = $children);
        } else if (!is_null($this->resource->{$relationship})) {
            //    dump("Set children: " . $children->join(","));
            $this->resource->{$relationship}->__requestedWith = $children;
        }
        return $this->resource->{$relationship};
    }

    protected function asDate($field)
    {
        return is_null($this->{$field}) ? null : $this->{$field}->toRfc3339String();
    }

    protected function asJson($field)
    {
        return is_array($this->{$field}) ? $this->{$field} : json_decode($this->{$field});
    }
}
