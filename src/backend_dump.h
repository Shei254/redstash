/*
Copyright (c) 2012-2014 The REDSTASH Authors. All rights reserved.
Use of this source code is governed by a BSD-style license that can be
found in the LICENSE file.
*/
#ifndef REDSTASH_BACKEND_DUMP_H_
#define REDSTASH_BACKEND_DUMP_H_

#include "include.h"
#include "redstash/redstash.h"
#include "net/link.h"

class BackendDump{
private:
	struct run_arg{
		const Link *link;
		const BackendDump *backend;
	};
	static void* _run_thread(void *arg);
	REDSTASH *redstash;
public:
	BackendDump(REDSTASH *redstash);
	~BackendDump();
	void proc(const Link *link);
};

#endif
